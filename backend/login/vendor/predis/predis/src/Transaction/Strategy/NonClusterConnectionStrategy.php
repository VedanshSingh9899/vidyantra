<?php











namespace Predis\Transaction\Strategy;

use Predis\Command\CommandInterface;
use Predis\Command\Redis\DISCARD;
use Predis\Command\Redis\EXEC;
use Predis\Command\Redis\MULTI;
use Predis\Command\Redis\UNWATCH;
use Predis\Command\Redis\WATCH;
use Predis\Connection\NodeConnectionInterface;
use Predis\Connection\RelayConnection;
use Predis\Connection\Replication\ReplicationInterface;
use Predis\Response\ErrorInterface;
use Predis\Response\ServerException;
use Predis\Transaction\MultiExecState;
use Predis\Transaction\Response\BypassTransactionResponse;




abstract class NonClusterConnectionStrategy implements StrategyInterface
{
    


    protected $connection;

    


    protected $state;

    


    public function __construct($connection, MultiExecState $state)
    {
        $this->connection = $connection;
        $this->state = $state;
    }

    


    public function initializeTransaction(): bool
    {
        return 'OK' == $this->executeBypassingTransaction(new MULTI())->getResponse();
    }

    


    public function executeCommand(CommandInterface $command)
    {
        if ($this->state->isCAS()) {
            return $this->executeBypassingTransaction($command);
        }

        return $this->connection->executeCommand($command);
    }

    


    public function executeTransaction()
    {
        return $this->executeBypassingTransaction(new EXEC())->getResponse();
    }

    


    public function multi()
    {
        return $this->executeBypassingTransaction(new MULTI())->getResponse();
    }

    


    public function watch(array $keys)
    {
        $watch = new WATCH();
        $watch->setArguments($keys);

        return $this->executeBypassingTransaction($watch)->getResponse();
    }

    


    public function unwatch()
    {
        return $this->connection->executeCommand(new UNWATCH());
    }

    


    public function discard()
    {
        return $this->executeBypassingTransaction(new DISCARD())->getResponse();
    }

    






    protected function executeBypassingTransaction(CommandInterface $command): BypassTransactionResponse
    {
        try {
            $response = $this->connection->executeCommand($command);
        } catch (ServerException $exception) {
            if (!$this->connection instanceof RelayConnection) {
                throw $exception;
            }

            if (strcasecmp($command->getId(), 'EXEC') != 0) {
                throw $exception;
            }

            if (!strpos($exception->getMessage(), 'RELAY_ERR_REDIS')) {
                throw $exception;
            }

            return new BypassTransactionResponse(null);
        }

        if ($response instanceof ErrorInterface) {
            throw new ServerException($response->getMessage());
        }

        return new BypassTransactionResponse($response);
    }
}
