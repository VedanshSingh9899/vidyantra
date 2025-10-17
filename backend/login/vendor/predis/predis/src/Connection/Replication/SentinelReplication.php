<?php











namespace Predis\Connection\Replication;

use InvalidArgumentException;
use Predis\Command\Command;
use Predis\Command\CommandInterface;
use Predis\Command\RawCommand;
use Predis\Connection\AbstractAggregateConnection;
use Predis\Connection\ConnectionException;
use Predis\Connection\FactoryInterface as ConnectionFactoryInterface;
use Predis\Connection\NodeConnectionInterface;
use Predis\Connection\Parameters;
use Predis\Connection\ParametersInterface;
use Predis\Replication\ReplicationStrategy;
use Predis\Replication\RoleException;
use Predis\Response\Error;
use Predis\Response\ErrorInterface as ErrorResponseInterface;
use Predis\Response\ServerException;
use Throwable;





class SentinelReplication extends AbstractAggregateConnection implements ReplicationInterface
{
    


    protected $master;

    


    protected $slaves = [];

    


    protected $pool = [];

    


    protected $current;

    


    protected $service;

    


    protected $connectionFactory;

    


    protected $strategy;

    


    protected $sentinels = [];

    


    protected $sentinelIndex = 0;

    


    protected $sentinelConnection;

    


    protected $sentinelTimeout = 0.100;

    








    protected $retryLimit = 20;

    





    protected $retryWait = 1000;

    




    protected $updateSentinels = false;

    





    public function __construct(
        $service,
        array $sentinels,
        ConnectionFactoryInterface $connectionFactory,
        ?ReplicationStrategy $strategy = null
    ) {
        $this->sentinels = $sentinels;
        $this->service = $service;
        $this->connectionFactory = $connectionFactory;
        $this->strategy = $strategy ?: new ReplicationStrategy();
    }

    







    public function setSentinelTimeout($timeout)
    {
        $this->sentinelTimeout = (float) $timeout;
    }

    








    public function setRetryLimit($retry)
    {
        $this->retryLimit = (int) $retry;
    }

    





    public function setRetryWait($milliseconds)
    {
        $this->retryWait = (float) $milliseconds;
    }

    




    public function setUpdateSentinels($update)
    {
        $this->updateSentinels = (bool) $update;
    }

    


    protected function reset()
    {
        $this->current = null;
    }

    


    protected function wipeServerList()
    {
        $this->reset();

        $this->master = null;
        $this->slaves = [];
        $this->pool = [];
    }

    


    public function add(NodeConnectionInterface $connection)
    {
        $parameters = $connection->getParameters();
        $role = $parameters->role;

        if ('master' === $role) {
            $this->master = $connection;
        } elseif ('sentinel' === $role) {
            $this->sentinels[] = $connection;

            
            return;
        } else {
            
            $this->slaves[] = $connection;
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
        } elseif (false !== $id = array_search($connection, $this->sentinels, true)) {
            unset($this->sentinels[$id]);

            return true;
        } else {
            return false;
        }

        unset($this->pool[(string) $connection]);

        $this->reset();

        return true;
    }

    




    protected function createSentinelConnection($parameters)
    {
        if ($parameters instanceof NodeConnectionInterface) {
            return $parameters;
        }

        if (is_string($parameters)) {
            $parameters = Parameters::parse($parameters);
        }

        if (is_array($parameters)) {
            
            
            
            $parameters['database'] = null;

            
            
            if (!isset($parameters['password'])) {
                $parameters['password'] = null;
            }

            if (!isset($parameters['timeout'])) {
                $parameters['timeout'] = $this->sentinelTimeout;
            }
        }

        return $this->connectionFactory->create($parameters);
    }

    






    public function getSentinelConnection()
    {
        if (!$this->sentinelConnection) {
            if ($this->sentinelIndex >= count($this->sentinels)) {
                $this->sentinelIndex = 0;
                throw new \Predis\ClientException('No sentinel server available for autodiscovery.');
            }

            $sentinel = $this->sentinels[$this->sentinelIndex];
            ++$this->sentinelIndex;
            $this->sentinelConnection = $this->createSentinelConnection($sentinel);
        }

        return $this->sentinelConnection;
    }

    


    public function updateSentinels()
    {
        SENTINEL_QUERY: {
            $sentinel = $this->getSentinelConnection();

            try {
                $payload = $sentinel->executeCommand(
                    RawCommand::create('SENTINEL', 'sentinels', $this->service)
                );

                $this->sentinels = [];
                $this->sentinelIndex = 0;
                
                $this->sentinels[] = $sentinel->getParameters()->toArray();

                foreach ($payload as $sentinel) {
                    $this->sentinels[] = [
                        'host' => $sentinel[3],
                        'port' => $sentinel[5],
                        'role' => 'sentinel',
                    ];
                }
            } catch (ConnectionException $exception) {
                $this->sentinelConnection = null;

                goto SENTINEL_QUERY;
            }
        }
    }

    


    public function querySentinel()
    {
        $this->wipeServerList();

        $this->updateSentinels();
        $this->getMaster();
        $this->getSlaves();
    }

    





    private function handleSentinelErrorResponse(NodeConnectionInterface $sentinel, ErrorResponseInterface $error)
    {
        if ($error->getErrorType() === 'IDONTKNOW') {
            throw new ConnectionException($sentinel, $error->getMessage());
        } else {
            throw new ServerException($error->getMessage());
        }
    }

    







    protected function querySentinelForMaster(NodeConnectionInterface $sentinel, $service)
    {
        $payload = $sentinel->executeCommand(
            RawCommand::create('SENTINEL', 'get-master-addr-by-name', $service)
        );

        if ($payload === null) {
            throw new ServerException('ERR No such master with that name');
        }

        if ($payload instanceof ErrorResponseInterface) {
            $this->handleSentinelErrorResponse($sentinel, $payload);
        }

        return [
            'host' => $payload[0],
            'port' => $payload[1],
            'role' => 'master',
        ];
    }

    







    protected function querySentinelForSlaves(NodeConnectionInterface $sentinel, $service)
    {
        $slaves = [];

        $payload = $sentinel->executeCommand(
            RawCommand::create('SENTINEL', 'slaves', $service)
        );

        if ($payload instanceof ErrorResponseInterface) {
            $this->handleSentinelErrorResponse($sentinel, $payload);
        }

        foreach ($payload as $slave) {
            $flags = explode(',', $slave[9]);

            if (array_intersect($flags, ['s_down', 'o_down', 'disconnected'])) {
                continue;
            }

            
            if (isset($slave[31]) && $slave[31] === 'err') {
                continue;
            }

            $slaves[] = [
                'host' => $slave[3],
                'port' => $slave[5],
                'role' => 'slave',
            ];
        }

        return $slaves;
    }

    


    public function getCurrent()
    {
        return $this->current;
    }

    


    public function getMaster()
    {
        if ($this->master) {
            return $this->master;
        }

        if ($this->updateSentinels) {
            $this->updateSentinels();
        }

        SENTINEL_QUERY: {
            $sentinel = $this->getSentinelConnection();

            try {
                $masterParameters = $this->querySentinelForMaster($sentinel, $this->service);
                $masterConnection = $this->connectionFactory->create($masterParameters);

                $this->add($masterConnection);
            } catch (ConnectionException $exception) {
                $this->sentinelConnection = null;

                goto SENTINEL_QUERY;
            }
        }

        return $masterConnection;
    }

    


    public function getSlaves()
    {
        if ($this->slaves) {
            return array_values($this->slaves);
        }

        if ($this->updateSentinels) {
            $this->updateSentinels();
        }

        SENTINEL_QUERY: {
            $sentinel = $this->getSentinelConnection();

            try {
                $slavesParameters = $this->querySentinelForSlaves($sentinel, $this->service);

                foreach ($slavesParameters as $slaveParameters) {
                    $this->add($this->connectionFactory->create($slaveParameters));
                }
            } catch (ConnectionException $exception) {
                $this->sentinelConnection = null;

                goto SENTINEL_QUERY;
            }
        }

        return array_values($this->slaves);
    }

    




    protected function pickSlave()
    {
        $slaves = $this->getSlaves();

        return $slaves
            ? $slaves[rand(1, count($slaves)) - 1]
            : null;
    }

    






    private function getConnectionInternal(CommandInterface $command)
    {
        if (!$this->current) {
            if ($this->strategy->isReadOperation($command) && $slave = $this->pickSlave()) {
                $this->current = $slave;
            } else {
                $this->current = $this->getMaster();
            }

            return $this->current;
        }

        if ($this->current === $this->master) {
            return $this->current;
        }

        if (!$this->strategy->isReadOperation($command)) {
            $this->current = $this->getMaster();
        }

        return $this->current;
    }

    







    protected function assertConnectionRole(NodeConnectionInterface $connection, $role)
    {
        $role = strtolower($role);
        $actualRole = $connection->executeCommand(RawCommand::create('ROLE'));

        if ($actualRole instanceof Error) {
            throw new ConnectionException($connection, $actualRole->getMessage());
        }

        if ($role !== $actualRole[0]) {
            throw new RoleException($connection, "Expected $role but got $actualRole[0] [$connection]");
        }
    }

    


    public function getConnectionByCommand(CommandInterface $command)
    {
        $connection = $this->getConnectionInternal($command);

        if (!$connection->isConnected()) {
            
            
            $expectedRole = $this->strategy->isReadOperation($command) && $this->slaves ? 'slave' : 'master';
            $this->assertConnectionRole($connection, $expectedRole);
        }

        return $connection;
    }

    


    public function getConnectionById($id)
    {
        return $this->pool[$id] ?? null;
    }

    






    public function getConnectionByRole($role)
    {
        if ($role === 'master') {
            return $this->getMaster();
        } elseif ($role === 'slave') {
            return $this->pickSlave();
        } elseif ($role === 'sentinel') {
            return $this->getSentinelConnection();
        } else {
            return null;
        }
    }

    







    public function switchTo(NodeConnectionInterface $connection)
    {
        if ($connection && $connection === $this->current) {
            return;
        }

        if ($connection !== $this->master && !in_array($connection, $this->slaves, true)) {
            throw new InvalidArgumentException('Invalid connection or connection not found.');
        }

        $connection->connect();

        if ($this->current) {
            $this->current->disconnect();
        }

        $this->current = $connection;
    }

    


    public function switchToMaster()
    {
        $connection = $this->getConnectionByRole('master');
        $this->switchTo($connection);
    }

    


    public function switchToSlave()
    {
        $connection = $this->getConnectionByRole('slave');
        $this->switchTo($connection);
    }

    


    public function isConnected()
    {
        return $this->current ? $this->current->isConnected() : false;
    }

    


    public function connect()
    {
        if (!$this->current) {
            if (!$this->current = $this->pickSlave()) {
                $this->current = $this->getMaster();
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

    








    private function retryCommandOnFailure(CommandInterface $command, $method)
    {
        $retries = 0;

        while ($retries <= $this->retryLimit) {
            try {
                $response = $this->getConnectionByCommand($command)->$method($command);
                if ($response instanceof Error && $response->getErrorType() === 'LOADING') {
                    throw new ConnectionException($this->current, $response->getMessage());
                }
                break;
            } catch (Throwable $exception) {
                $this->wipeServerList();

                if ($exception instanceof ConnectionException) {
                    $exception->getConnection()->disconnect();
                }

                if ($retries === $this->retryLimit) {
                    throw $exception;
                }

                usleep($this->retryWait * 1000);

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
        return $this->retryCommandOnFailure($command, __FUNCTION__);
    }

    




    public function getReplicationStrategy()
    {
        return $this->strategy;
    }

    


    public function __sleep()
    {
        return [
            'master', 'slaves', 'pool', 'service', 'sentinels', 'connectionFactory', 'strategy',
        ];
    }

    


    public function getParameters(): ?ParametersInterface
    {
        if (isset($this->master)) {
            return $this->master->getParameters();
        }

        if (!empty($this->slaves)) {
            return $this->slaves[0]->getParameters();
        }

        if (!empty($this->sentinels)) {
            return $this->sentinels[0]->getParameters();
        }

        return null;
    }
}
