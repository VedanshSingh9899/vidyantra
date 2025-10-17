<?php











namespace Predis\Connection\Cluster;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Predis\Cluster\PredisStrategy;
use Predis\Cluster\StrategyInterface;
use Predis\Command\CommandInterface;
use Predis\Connection\AbstractAggregateConnection;
use Predis\Connection\NodeConnectionInterface;
use Predis\Connection\ParametersInterface;
use Predis\NotSupportedException;
use ReturnTypeWillChange;
use Traversable;





class PredisCluster extends AbstractAggregateConnection implements ClusterInterface, IteratorAggregate, Countable
{
    


    private $pool = [];

    


    private $aliases = [];

    


    private $strategy;

    


    private $distributor;

    


    private $connectionParameters;

    



    public function __construct(ParametersInterface $parameters, ?StrategyInterface $strategy = null)
    {
        $this->connectionParameters = $parameters;
        $this->strategy = $strategy ?: new PredisStrategy();
        $this->distributor = $this->strategy->getDistributor();
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

    




    protected function getRandomConnection()
    {
        if (!$this->pool) {
            return null;
        }

        return $this->pool[array_rand($this->pool)];
    }

    


    public function disconnect()
    {
        foreach ($this->pool as $connection) {
            $connection->disconnect();
        }
    }

    


    public function add(NodeConnectionInterface $connection)
    {
        $parameters = $connection->getParameters();

        $this->pool[(string) $connection] = $connection;

        if (isset($parameters->alias)) {
            $this->aliases[$parameters->alias] = $connection;
        }

        $this->distributor->add($connection, $parameters->weight);
    }

    


    public function remove(NodeConnectionInterface $connection)
    {
        if (false !== $id = array_search($connection, $this->pool, true)) {
            unset($this->pool[$id]);
            $this->distributor->remove($connection);

            if ($this->aliases && $alias = $connection->getParameters()->alias) {
                unset($this->aliases[$alias]);
            }

            return true;
        }

        return false;
    }

    


    public function getConnectionByCommand(CommandInterface $command)
    {
        $slot = $this->strategy->getSlot($command);

        if (!isset($slot)) {
            throw new NotSupportedException(
                "Cannot use '{$command->getId()}' over clusters of connections."
            );
        }

        return $this->distributor->getBySlot($slot);
    }

    


    public function getConnectionById($id)
    {
        return $this->pool[$id] ?? null;
    }

    






    public function getConnectionByAlias($alias)
    {
        return $this->aliases[$alias] ?? null;
    }

    






    public function getConnectionBySlot($slot)
    {
        return $this->distributor->getBySlot($slot);
    }

    






    public function getConnectionByKey($key)
    {
        $hash = $this->strategy->getSlotByKey($key);

        return $this->distributor->getBySlot($hash);
    }

    


    public function getClusterStrategy(): StrategyInterface
    {
        return $this->strategy;
    }

    


    #[ReturnTypeWillChange]
    public function count()
    {
        return count($this->pool);
    }

    


    #[ReturnTypeWillChange]
    public function getIterator()
    {
        return new ArrayIterator($this->pool);
    }

    


    public function writeRequest(CommandInterface $command)
    {
        $this->getConnectionByCommand($command)->writeRequest($command);
    }

    


    public function readResponse(CommandInterface $command)
    {
        return $this->getConnectionByCommand($command)->readResponse($command);
    }

    


    public function executeCommand(CommandInterface $command)
    {
        return $this->getConnectionByCommand($command)->executeCommand($command);
    }

    


    public function getParameters(): ParametersInterface
    {
        return $this->connectionParameters;
    }

    


    public function executeCommandOnEachNode(CommandInterface $command): array
    {
        $responses = [];

        foreach ($this->pool as $connection) {
            $responses[] = $connection->executeCommand($command);
        }

        return $responses;
    }
}
