<?php











namespace Predis\Connection\Replication;

use InvalidArgumentException;
use Predis\ClientException;
use Predis\Command\Command;
use Predis\Command\CommandInterface;
use Predis\Command\RawCommand;
use Predis\Connection\AbstractAggregateConnection;
use Predis\Connection\ConnectionException;
use Predis\Connection\FactoryInterface;
use Predis\Connection\NodeConnectionInterface;
use Predis\Connection\ParametersInterface;
use Predis\Replication\MissingMasterException;
use Predis\Replication\ReplicationStrategy;
use Predis\Response\ErrorInterface as ResponseErrorInterface;





class MasterSlaveReplication extends AbstractAggregateConnection implements ReplicationInterface
{
    


    protected $strategy;

    


    protected $master;

    


    protected $slaves = [];

    


    protected $pool = [];

    


    protected $aliases = [];

    


    protected $current;

    


    protected $autoDiscovery = false;

    


    protected $connectionFactory;

    


    public function __construct(?ReplicationStrategy $strategy = null)
    {
        $this->strategy = $strategy ?: new ReplicationStrategy();
    }

    




    public function setAutoDiscovery($value)
    {
        if (!$this->connectionFactory) {
            throw new ClientException('Automatic discovery requires a connection factory');
        }

        $this->autoDiscovery = (bool) $value;
    }

    





    public function setConnectionFactory(FactoryInterface $connectionFactory)
    {
        $this->connectionFactory = $connectionFactory;
    }

    


    protected function reset()
    {
        $this->current = null;
    }

    


    public function add(NodeConnectionInterface $connection)
    {
        $parameters = $connection->getParameters();

        if ('master' === $parameters->role) {
            $this->master = $connection;
        } else {
            
            $this->slaves[] = $connection;
        }

        if (isset($parameters->alias)) {
            $this->aliases[$parameters->alias] = $connection;
        }

        $this->pool[(string) $connection] = $connection;

        $this->reset();
    }

    


    public function remove(NodeConnectionInterface $connection)
    {
        if ($connection === $this->master) {
            $this->master = null;
        } elseif (false !== $id = array_search($connection, $this->slaves, true)) {
            unset($this->slaves[$id]);
        } else {
            return false;
        }

        unset($this->pool[(string) $connection]);

        if ($this->aliases && $alias = $connection->getParameters()->alias) {
            unset($this->aliases[$alias]);
        }

        $this->reset();

        return true;
    }

    


    public function getConnectionByCommand(CommandInterface $command)
    {
        if (!$this->current) {
            if ($this->strategy->isReadOperation($command) && $slave = $this->pickSlave()) {
                $this->current = $slave;
            } else {
                $this->current = $this->getMasterOrDie();
            }

            return $this->current;
        }

        if ($this->current === $master = $this->getMasterOrDie()) {
            return $master;
        }

        if (!$this->strategy->isReadOperation($command) || !$this->slaves) {
            $this->current = $master;
        }

        return $this->current;
    }

    


    public function getConnectionById($id)
    {
        return $this->pool[$id] ?? null;
    }

    






    public function getConnectionByAlias($alias)
    {
        return $this->aliases[$alias] ?? null;
    }

    






    public function getConnectionByRole($role)
    {
        if ($role === 'master') {
            return $this->getMaster();
        } elseif ($role === 'slave') {
            return $this->pickSlave();
        }

        return null;
    }

    




    public function switchTo(NodeConnectionInterface $connection)
    {
        if ($connection && $connection === $this->current) {
            return;
        }

        if ($connection !== $this->master && !in_array($connection, $this->slaves, true)) {
            throw new InvalidArgumentException('Invalid connection or connection not found.');
        }

        $this->current = $connection;
    }

    


    public function switchToMaster()
    {
        if (!$connection = $this->getConnectionByRole('master')) {
            throw new InvalidArgumentException('Invalid connection or connection not found.');
        }

        $this->switchTo($connection);
    }

    


    public function switchToSlave()
    {
        if (!$connection = $this->getConnectionByRole('slave')) {
            throw new InvalidArgumentException('Invalid connection or connection not found.');
        }

        $this->switchTo($connection);
    }

    


    public function getCurrent()
    {
        return $this->current;
    }

    


    public function getMaster()
    {
        return $this->master;
    }

    




    private function getMasterOrDie()
    {
        if (!$connection = $this->getMaster()) {
            throw new MissingMasterException('No master server available for replication');
        }

        return $connection;
    }

    


    public function getSlaves()
    {
        return $this->slaves;
    }

    




    public function getReplicationStrategy()
    {
        return $this->strategy;
    }

    




    protected function pickSlave()
    {
        if (!$this->slaves) {
            return null;
        }

        return $this->slaves[array_rand($this->slaves)];
    }

    


    public function isConnected()
    {
        return $this->current ? $this->current->isConnected() : false;
    }

    


    public function connect()
    {
        if (!$this->current) {
            if (!$this->current = $this->pickSlave()) {
                if (!$this->current = $this->getMaster()) {
                    throw new ClientException('No available connection for replication');
                }
            }
        }

        $this->current->connect();
    }

    


    public function disconnect()
    {
        foreach ($this->pool as $connection) {
            $connection->disconnect();
        }
    }

    






    private function handleInfoResponse($response)
    {
        $info = [];

        foreach (preg_split('/\r?\n/', $response) as $row) {
            if (strpos($row, ':') === false) {
                continue;
            }

            [$k, $v] = explode(':', $row, 2);
            $info[$k] = $v;
        }

        return $info;
    }

    


    public function discover()
    {
        if (!$this->connectionFactory) {
            throw new ClientException('Discovery requires a connection factory');
        }

        while (true) {
            try {
                if ($connection = $this->getMaster()) {
                    $this->discoverFromMaster($connection, $this->connectionFactory);
                    break;
                } elseif ($connection = $this->pickSlave()) {
                    $this->discoverFromSlave($connection, $this->connectionFactory);
                    break;
                } else {
                    throw new ClientException('No connection available for discovery');
                }
            } catch (ConnectionException $exception) {
                $this->remove($connection);
            }
        }
    }

    





    protected function discoverFromMaster(NodeConnectionInterface $connection, FactoryInterface $connectionFactory)
    {
        $response = $connection->executeCommand(RawCommand::create('INFO', 'REPLICATION'));
        $replication = $this->handleInfoResponse($response);

        if ($replication['role'] !== 'master') {
            throw new ClientException("Role mismatch (expected master, got slave) [$connection]");
        }

        $this->slaves = [];

        foreach ($replication as $k => $v) {
            $parameters = null;

            if (strpos($k, 'slave') === 0 && preg_match('/ip=(?P<host>.*),port=(?P<port>\d+)/', $v, $parameters)) {
                $slaveConnection = $connectionFactory->create([
                    'host' => $parameters['host'],
                    'port' => $parameters['port'],
                    'role' => 'slave',
                ]);

                $this->add($slaveConnection);
            }
        }
    }

    





    protected function discoverFromSlave(NodeConnectionInterface $connection, FactoryInterface $connectionFactory)
    {
        $response = $connection->executeCommand(RawCommand::create('INFO', 'REPLICATION'));
        $replication = $this->handleInfoResponse($response);

        if ($replication['role'] !== 'slave') {
            throw new ClientException("Role mismatch (expected slave, got master) [$connection]");
        }

        $masterConnection = $connectionFactory->create([
            'host' => $replication['master_host'],
            'port' => $replication['master_port'],
            'role' => 'master',
        ]);

        $this->add($masterConnection);

        $this->discoverFromMaster($masterConnection, $connectionFactory);
    }

    







    private function retryCommandOnFailure(CommandInterface $command, $method)
    {
        while (true) {
            try {
                $connection = $this->getConnectionByCommand($command);
                $response = $connection->$method($command);

                if ($response instanceof ResponseErrorInterface && $response->getErrorType() === 'LOADING') {
                    throw new ConnectionException($connection, "Redis is loading the dataset in memory [$connection]");
                }

                break;
            } catch (ConnectionException $exception) {
                $connection = $exception->getConnection();
                $connection->disconnect();

                if ($connection === $this->master && !$this->autoDiscovery) {
                    
                    
                    
                    throw $exception;
                } else {
                    
                    
                    $this->remove($connection);
                }

                
                if (!$this->slaves && !$this->master) {
                    throw $exception;
                } elseif ($this->autoDiscovery) {
                    $this->discover();
                }
            } catch (MissingMasterException $exception) {
                if ($this->autoDiscovery) {
                    $this->discover();
                } else {
                    throw $exception;
                }
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
        return $this->retryCommandOnFailure($command, __FUNCTION__);
    }

    


    public function __sleep()
    {
        return ['master', 'slaves', 'pool', 'aliases', 'strategy'];
    }

    


    public function getParameters(): ?ParametersInterface
    {
        if (isset($this->master)) {
            return $this->master->getParameters();
        }

        $slave = $this->pickSlave();

        if (null !== $slave) {
            return $slave->getParameters();
        }

        return null;
    }
}
