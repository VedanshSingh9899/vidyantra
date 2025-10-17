<?php











namespace Predis\Transaction\Strategy;

use Predis\Command\CommandInterface;
use Predis\Command\Redis\DISCARD;
use Predis\Command\Redis\EXEC;
use Predis\Command\Redis\MULTI;
use Predis\Command\Redis\UNWATCH;
use Predis\Command\Redis\WATCH;
use Predis\Connection\Cluster\ClusterInterface;
use Predis\Response\Error;
use Predis\Response\Status;
use Predis\Transaction\Exception\TransactionException;
use Predis\Transaction\MultiExecState;
use Relay\Relay;
use SplQueue;

class ClusterConnectionStrategy implements StrategyInterface
{
    


    private $connection;

    




    private $slot;

    





    private $commandsQueue;

    




    private $isInitialized = false;

    


    private $clusterStrategy;

    


    private $state;

    public function __construct(ClusterInterface $connection, MultiExecState $state)
    {
        $this->commandsQueue = new SplQueue();
        $this->connection = $connection;
        $this->state = $state;
        $this->clusterStrategy = $this->connection->getClusterStrategy();
    }

    


    public function executeCommand(CommandInterface $command)
    {
        if (!$this->isInitialized) {
            throw new TransactionException('Transaction context should be initialized first');
        }

        $commandSlot = $this->clusterStrategy->getSlot($command);

        if (null === $this->slot) {
            $this->slot = $commandSlot;
        }

        if (null === $commandSlot && null !== $this->slot) {
            $command->setSlot($this->slot);
        }

        if (is_int($commandSlot) && $commandSlot !== $this->slot) {
            return new Error(
                'To be able to execute a transaction against cluster, all commands should operate on the same hash slot'
            );
        }

        $this->commandsQueue->enqueue($command);

        return new Status('QUEUED');
    }

    


    public function initializeTransaction(): bool
    {
        if ($this->isInitialized) {
            return true;
        }

        $this->commandsQueue->enqueue(new MULTI());
        $this->isInitialized = true;

        return true;
    }

    


    public function executeTransaction()
    {
        if (!$this->isInitialized) {
            throw new TransactionException('Transaction context should be initialized first');
        }

        $exec = new EXEC();

        
        $multi = $this->commandsQueue->dequeue();
        $multiResp = $this->setSlotAndExecute($multi);

        
        if (('OK' != $multiResp) && !$multiResp instanceof Relay) {
            $this->slot = null;

            return null;
        }

        
        while (!$this->commandsQueue->isEmpty()) {
            
            $command = $this->commandsQueue->dequeue();
            $commandResp = $this->setSlotAndExecute($command);

            if (('QUEUED' != $commandResp) && !$commandResp instanceof Relay) {
                $this->slot = null;

                return null;
            }
        }

        
        $exec = $this->setSlotAndExecute($exec);
        $this->slot = null;

        return $exec;
    }

    


    public function multi()
    {
        $response = $this->setSlotAndExecute(new MULTI());

        if ('OK' == $response) {
            $this->isInitialized = true;
        }

        return $response;
    }

    


    public function watch(array $keys)
    {
        if (!$this->clusterStrategy->checkSameSlotForKeys($keys)) {
            throw new TransactionException('WATCHed keys should point to the same hash slot');
        }

        $this->slot = $this->clusterStrategy->getSlotByKey($keys[0]);

        $watch = new WATCH();
        $watch->setArguments($keys);

        $response = 'OK' == $this->setSlotAndExecute($watch);

        if ($this->state->check(MultiExecState::CAS)) {
            $this->initializeTransaction();
        }

        return $response;
    }

    


    public function discard()
    {
        return $this->setSlotAndExecute(new DISCARD());
    }

    


    public function unwatch()
    {
        return $this->setSlotAndExecute(new UNWATCH());
    }

    





    private function setSlotAndExecute(CommandInterface $command)
    {
        if (null !== $this->slot) {
            $command->setSlot($this->slot);
        }

        return $this->connection->executeCommand($command);
    }
}
