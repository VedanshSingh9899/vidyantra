<?php











namespace Predis\Connection\Cluster;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use OutOfBoundsException;
use Predis\ClientException;
use Predis\Cluster\RedisStrategy as RedisClusterStrategy;
use Predis\Cluster\SlotMap;
use Predis\Cluster\StrategyInterface;
use Predis\Command\Command;
use Predis\Command\CommandInterface;
use Predis\Command\RawCommand;
use Predis\Connection\AbstractAggregateConnection;
use Predis\Connection\ConnectionException;
use Predis\Connection\FactoryInterface;
use Predis\Connection\NodeConnectionInterface;
use Predis\Connection\ParametersInterface;
use Predis\NotSupportedException;
use Predis\Response\Error as ErrorResponse;
use Predis\Response\ErrorInterface as ErrorResponseInterface;
use Predis\Response\ServerException;
use ReturnTypeWillChange;
use Throwable;
use Traversable;





















class RedisCluster extends AbstractAggregateConnection implements ClusterInterface, IteratorAggregate, Countable
{
    private $useClusterSlots = true;

    


    private $pool = [];
    private $slots = [];
    private $slotmap;
    private $strategy;
    private $connections;
    private $retryLimit = 5;
    private $retryInterval = 10;

    


    private $readTimeout = 1000;

    


    private $connectionParameters;

    




    public function __construct(
        FactoryInterface $connections,
        ParametersInterface $parameters,
        ?StrategyInterface $strategy = null,
        ?int $readTimeout = null
    ) {
        $this->connections = $connections;
        $this->connectionParameters = $parameters;
        $this->strategy = $strategy ?: new RedisClusterStrategy();
        $this->slotmap = new SlotMap();

        if (!is_null($readTimeout)) {
            $this->readTimeout = $readTimeout;
        }
    }

    








    public function setRetryLimit($retry)
    {
        $this->retryLimit = (int) $retry;
    }

    




    public function setRetryInterval($retryInterval)
    {
        $this->retryInterval = (int) $retryInterval;
    }

    




    public function getRetryInterval()
    {
        return (int) $this->retryInterval;
    }

    


    public function isConnected()
    {
        foreach ($this->pool as $connection) {
            if ($connection->isConnected()) {
                return true;
            }
        }

        return false;
    }

    


    public function connect()
    {
        foreach ($this->pool as $connection) {
            $connection->connect();
        }
    }

    


    public function disconnect()
    {
        foreach ($this->pool as $connection) {
            $connection->disconnect();
        }
    }

    


    public function add(NodeConnectionInterface $connection)
    {
        $this->pool[(string) $connection] = $connection;
        $this->slotmap->reset();
    }

    


    public function remove(NodeConnectionInterface $connection)
    {
        if (false !== $id = array_search($connection, $this->pool, true)) {
            $this->slotmap->reset();
            $this->slots = array_diff($this->slots, [$connection]);
            unset($this->pool[$id]);

            return true;
        }

        return false;
    }

    






    public function removeById($connectionID)
    {
        if (isset($this->pool[$connectionID])) {
            $this->slotmap->reset();
            $this->slots = array_diff($this->slots, [$connectionID]);
            unset($this->pool[$connectionID]);

            return true;
        }

        return false;
    }

    








    public function buildSlotMap()
    {
        $this->slotmap->reset();

        foreach ($this->pool as $connectionID => $connection) {
            $parameters = $connection->getParameters();

            if (!isset($parameters->slots)) {
                continue;
            }

            foreach (explode(',', $parameters->slots) as $slotRange) {
                $slots = explode('-', $slotRange, 2);

                if (!isset($slots[1])) {
                    $slots[1] = $slots[0];
                }

                $this->slotmap->setSlots($slots[0], $slots[1], $connectionID);
            }
        }
    }

    










    private function queryClusterNodeForSlotMap(NodeConnectionInterface $connection)
    {
        $retries = 0;
        $retryAfter = $this->retryInterval;
        $command = RawCommand::create('CLUSTER', 'SLOTS');

        while ($retries <= $this->retryLimit) {
            try {
                $response = $connection->executeCommand($command);
                break;
            } catch (ConnectionException $exception) {
                $connection = $exception->getConnection();
                $connection->disconnect();

                $this->remove($connection);

                if ($retries === $this->retryLimit) {
                    throw $exception;
                }

                if (!$connection = $this->getRandomConnection()) {
                    throw new ClientException('No connections left in the pool for `CLUSTER SLOTS`');
                }

                usleep($retryAfter * 1000);
                $retryAfter *= 2;
                ++$retries;
            }
        }

        return $response;
    }

    






    public function askSlotMap(?NodeConnectionInterface $connection = null)
    {
        if (!$connection && !$connection = $this->getRandomConnection()) {
            return;
        }

        $this->slotmap->reset();

        $response = $this->queryClusterNodeForSlotMap($connection);

        foreach ($response as $slots) {
            
            
            [$start, $end, $master] = $slots;

            if ($master[0] === '') {
                $this->slotmap->setSlots($start, $end, (string) $connection);
            } else {
                $this->slotmap->setSlots($start, $end, "{$master[0]}:{$master[1]}");
            }
        }
    }

    








    protected function guessNode($slot)
    {
        if (!$this->pool) {
            throw new ClientException('No connections available in the pool');
        }

        if ($this->slotmap->isEmpty()) {
            $this->buildSlotMap();
        }

        if ($node = $this->slotmap[$slot]) {
            return $node;
        }

        $count = count($this->pool);
        $index = min((int) ($slot / (int) (16384 / $count)), $count - 1);
        $nodes = array_keys($this->pool);

        return $nodes[$index];
    }

    






    protected function createConnection($connectionID)
    {
        $separator = strrpos($connectionID, ':');

        return $this->connections->create([
            'host' => substr($connectionID, 0, $separator),
            'port' => substr($connectionID, $separator + 1),
        ]);
    }

    


    public function getConnectionByCommand(CommandInterface $command)
    {
        $slot = $this->strategy->getSlot($command);

        if (!isset($slot)) {
            throw new NotSupportedException(
                "Cannot use '{$command->getId()}' with redis-cluster."
            );
        }

        if (isset($this->slots[$slot])) {
            return $this->slots[$slot];
        } else {
            return $this->getConnectionBySlot($slot);
        }
    }

    







    public function getConnectionBySlot($slot)
    {
        if (!SlotMap::isValid($slot)) {
            throw new OutOfBoundsException("Invalid slot [$slot].");
        }

        if (isset($this->slots[$slot])) {
            return $this->slots[$slot];
        }

        $connectionID = $this->guessNode($slot);

        if (!$connection = $this->getConnectionById($connectionID)) {
            $connection = $this->createConnection($connectionID);
            $this->pool[$connectionID] = $connection;
        }

        return $this->slots[$slot] = $connection;
    }

    


    public function getConnectionById($connectionID)
    {
        return $this->pool[$connectionID] ?? null;
    }

    




    protected function getRandomConnection()
    {
        if (!$this->pool) {
            return null;
        }

        return $this->pool[array_rand($this->pool)];
    }

    






    protected function move(NodeConnectionInterface $connection, $slot)
    {
        $this->pool[(string) $connection] = $connection;
        $this->slots[(int) $slot] = $connection;
        $this->slotmap[(int) $slot] = $connection;
    }

    







    protected function onErrorResponse(CommandInterface $command, ErrorResponseInterface $error)
    {
        $details = explode(' ', $error->getMessage(), 2);

        switch ($details[0]) {
            case 'MOVED':
                return $this->onMovedResponse($command, $details[1]);

            case 'ASK':
                return $this->onAskResponse($command, $details[1]);

            default:
                return $error;
        }
    }

    








    protected function onMovedResponse(CommandInterface $command, $details)
    {
        [$slot, $connectionID] = explode(' ', $details, 2);

        
        
        $startPositionOfExtraDetails = strpos($connectionID, ' ');

        if ($startPositionOfExtraDetails !== false) {
            $connectionID = substr($connectionID, 0, $startPositionOfExtraDetails);
        }

        if (!$connection = $this->getConnectionById($connectionID)) {
            $connection = $this->createConnection($connectionID);
        }

        if ($this->useClusterSlots) {
            $this->askSlotMap($connection);
        }

        $this->move($connection, $slot);

        return $this->executeCommand($command);
    }

    








    protected function onAskResponse(CommandInterface $command, $details)
    {
        [$slot, $connectionID] = explode(' ', $details, 2);

        if (!$connection = $this->getConnectionById($connectionID)) {
            $connection = $this->createConnection($connectionID);
        }

        $connection->executeCommand(RawCommand::create('ASKING'));

        return $connection->executeCommand($command);
    }

    













    private function retryCommandOnFailure(CommandInterface $command, $method)
    {
        $retries = 0;
        $retryAfter = $this->retryInterval;

        while ($retries <= $this->retryLimit) {
            try {
                $response = $this->getConnectionByCommand($command)->$method($command);

                if ($response instanceof ErrorResponse) {
                    $message = $response->getMessage();

                    if (strpos($message, 'CLUSTERDOWN') !== false) {
                        throw new ServerException($message);
                    }
                }

                break;
            } catch (Throwable $exception) {
                usleep($retryAfter * 1000);
                $retryAfter *= 2;

                if ($exception instanceof ConnectionException) {
                    $connection = $exception->getConnection();

                    if ($connection) {
                        $connection->disconnect();
                        $this->remove($connection);
                    }
                }

                if ($retries === $this->retryLimit) {
                    throw $exception;
                }

                if ($this->useClusterSlots) {
                    $this->askSlotMap();
                }

                ++$retries;
            }
        }

        return $response;
    }

    


    public function writeRequest(CommandInterface $command)
    {
        $this->retryCommandOnFailure($command, __FUNCTION__);
    }

    


    public function readResponse(CommandInterface $command)
    {
        return $this->retryCommandOnFailure($command, __FUNCTION__);
    }

    


    public function executeCommand(CommandInterface $command)
    {
        $response = $this->retryCommandOnFailure($command, __FUNCTION__);

        if ($response instanceof ErrorResponseInterface) {
            return $this->onErrorResponse($command, $response);
        }

        return $response;
    }

    


    public function executeCommandOnEachNode(CommandInterface $command): array
    {
        $responses = [];

        foreach ($this->pool as $connection) {
            $responses[] = $connection->executeCommand($command);
        }

        return $responses;
    }

    


    #[ReturnTypeWillChange]
    public function count()
    {
        return count($this->pool);
    }

    


    #[ReturnTypeWillChange]
    public function getIterator()
    {
        if ($this->slotmap->isEmpty()) {
            $this->useClusterSlots ? $this->askSlotMap() : $this->buildSlotMap();
        }

        $connections = [];

        foreach ($this->slotmap->getNodes() as $node) {
            if (!$connection = $this->getConnectionById($node)) {
                $this->add($connection = $this->createConnection($node));
            }

            $connections[] = $connection;
        }

        return new ArrayIterator($connections);
    }

    




    public function getSlotMap()
    {
        return $this->slotmap;
    }

    


    public function getClusterStrategy(): StrategyInterface
    {
        return $this->strategy;
    }

    





    public function getConnectionFactory()
    {
        return $this->connections;
    }

    












    public function useClusterSlots($value)
    {
        $this->useClusterSlots = (bool) $value;
    }

    


    public function getParameters(): ?ParametersInterface
    {
        return $this->connectionParameters;
    }

    




    public function read()
    {
        while (true) {
            foreach ($this->pool as $connection) {
                if ($connection->hasDataToRead()) {
                    return $connection->read();
                }
            }

            usleep($this->readTimeout);
        }
    }
}
