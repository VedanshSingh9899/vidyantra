<?php











namespace Predis;

use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;
use Predis\Command\CommandInterface;
use Predis\Command\Container\ContainerFactory;
use Predis\Command\Container\ContainerInterface;
use Predis\Command\RawCommand;
use Predis\Command\ScriptCommand;
use Predis\Configuration\Options;
use Predis\Configuration\OptionsInterface;
use Predis\Connection\ConnectionInterface;
use Predis\Connection\Parameters;
use Predis\Connection\ParametersInterface;
use Predis\Connection\RelayConnection;
use Predis\Consumer\PubSub\Consumer as PubSubConsumer;
use Predis\Consumer\PubSub\RelayConsumer as RelayPubSubConsumer;
use Predis\Consumer\Push\Consumer as PushConsumer;
use Predis\Monitor\Consumer as MonitorConsumer;
use Predis\Pipeline\Atomic;
use Predis\Pipeline\FireAndForget;
use Predis\Pipeline\Pipeline;
use Predis\Pipeline\RelayAtomic;
use Predis\Pipeline\RelayPipeline;
use Predis\Response\ErrorInterface as ErrorResponseInterface;
use Predis\Response\ResponseInterface;
use Predis\Response\ServerException;
use Predis\Transaction\MultiExec as MultiExecTransaction;
use ReturnTypeWillChange;
use RuntimeException;
use Traversable;










class Client implements ClientInterface, IteratorAggregate
{
    public const VERSION = '3.2.0';

    
    private $options;

    
    private $connection;

    
    private $commands;

    



    public function __construct($parameters = null, $options = null)
    {
        $this->options = static::createOptions($options ?? new Options());
        $this->connection = static::createConnection($this->options, $parameters ?? new Parameters());
        $this->commands = $this->options->commands;
    }

    







    protected static function createOptions($options)
    {
        if (is_array($options)) {
            return new Options($options);
        } elseif ($options instanceof OptionsInterface) {
            return $options;
        } else {
            throw new InvalidArgumentException('Invalid type for client options');
        }
    }

    























    protected static function createConnection(OptionsInterface $options, $parameters)
    {
        if ($parameters instanceof ConnectionInterface) {
            return $parameters;
        }

        if ($parameters instanceof ParametersInterface || is_string($parameters)) {
            return $options->connections->create($parameters);
        }

        if (is_array($parameters)) {
            if (!isset($parameters[0])) {
                return $options->connections->create($parameters);
            } elseif ($options->defined('cluster') && $initializer = $options->cluster) {
                return $initializer($parameters, true);
            } elseif ($options->defined('replication') && $initializer = $options->replication) {
                return $initializer($parameters, true);
            } elseif ($options->defined('aggregate') && $initializer = $options->aggregate) {
                return $initializer($parameters, false);
            } else {
                throw new InvalidArgumentException(
                    'Array of connection parameters requires `cluster`, `replication` or `aggregate` client option'
                );
            }
        }

        if (is_callable($parameters)) {
            $connection = call_user_func($parameters, $options);

            if (!$connection instanceof ConnectionInterface) {
                throw new InvalidArgumentException('Callable parameters must return a valid connection');
            }

            return $connection;
        }

        throw new InvalidArgumentException('Invalid type for connection parameters');
    }

    


    public function getCommandFactory()
    {
        return $this->commands;
    }

    


    public function getOptions()
    {
        return $this->options;
    }

    





























    public function getClientBy($selector, $value)
    {
        $selector = strtolower($selector);

        if (!in_array($selector, ['id', 'key', 'slot', 'role', 'alias', 'command'])) {
            throw new InvalidArgumentException("Invalid selector type: `$selector`");
        }

        if (!method_exists($this->connection, $method = "getConnectionBy$selector")) {
            $class = get_class($this->connection);
            throw new InvalidArgumentException("Selecting connection by $selector is not supported by $class");
        }

        if (!$connection = $this->connection->$method($value)) {
            throw new InvalidArgumentException("Cannot find a connection by $selector matching `$value`");
        }

        return new static($connection, $this->getOptions());
    }

    


    public function connect()
    {
        $this->connection->connect();
    }

    


    public function disconnect()
    {
        $this->connection->disconnect();
    }

    





    public function quit()
    {
        $this->disconnect();
    }

    




    public function isConnected()
    {
        return $this->connection->isConnected();
    }

    


    public function getConnection()
    {
        return $this->connection;
    }

    





    public function pack($value)
    {
        return $this->connection instanceof RelayConnection
            ? $this->connection->pack($value)
            : $value;
    }

    





    public function unpack($value)
    {
        return $this->connection instanceof RelayConnection
            ? $this->connection->unpack($value)
            : $value;
    }

    












    public function executeRaw(array $arguments, &$error = null)
    {
        $error = false;
        $commandID = array_shift($arguments);

        $response = $this->connection->executeCommand(
            new RawCommand($commandID, $arguments)
        );

        if ($response instanceof ResponseInterface) {
            if ($response instanceof ErrorResponseInterface) {
                $error = true;
            }

            return (string) $response;
        }

        return $response;
    }

    


    public function __call($commandID, $arguments)
    {
        return $this->executeCommand(
            $this->createCommand($commandID, $arguments)
        );
    }

    


    public function createCommand($commandID, $arguments = [])
    {
        return $this->commands->create($commandID, $arguments);
    }

    



    public function __get(string $name)
    {
        return ContainerFactory::create($this, $name);
    }

    




    public function __set(string $name, $value)
    {
        throw new RuntimeException('Not allowed');
    }

    



    public function __isset(string $name)
    {
        throw new RuntimeException('Not allowed');
    }

    


    public function executeCommand(CommandInterface $command)
    {
        $response = $this->connection->executeCommand($command);
        $parameters = $this->connection->getParameters();

        if ($response instanceof ResponseInterface) {
            if ($response instanceof ErrorResponseInterface) {
                $response = $this->onErrorResponse($command, $response);
            }

            return $response;
        }

        if ($parameters->protocol === 2) {
            return $command->parseResponse($response);
        }

        return $command->parseResp3Response($response);
    }

    








    protected function onErrorResponse(CommandInterface $command, ErrorResponseInterface $response)
    {
        if ($command instanceof ScriptCommand && $response->getErrorType() === 'NOSCRIPT') {
            $response = $this->executeCommand($command->getEvalCommand());

            if (!$response instanceof ResponseInterface) {
                $response = $command->parseResponse($response);
            }

            return $response;
        }

        if ($this->options->exceptions) {
            throw new ServerException($response->getMessage());
        }

        return $response;
    }

    










    private function sharedContextFactory($initializer, $argv = null)
    {
        switch (count($argv)) {
            case 0:
                return $this->$initializer();

            case 1:
                return is_array($argv[0])
                    ? $this->$initializer($argv[0])
                    : $this->$initializer(null, $argv[0]);

            case 2:
                [$arg0, $arg1] = $argv;

                return $this->$initializer($arg0, $arg1);

            default:
                return $this->$initializer($this, $argv);
        }
    }

    







    public function pipeline(...$arguments)
    {
        return $this->sharedContextFactory('createPipeline', func_get_args());
    }

    







    protected function createPipeline(?array $options = null, $callable = null)
    {
        if (isset($options['atomic']) && $options['atomic']) {
            $class = Atomic::class;
        } elseif (isset($options['fire-and-forget']) && $options['fire-and-forget']) {
            $class = FireAndForget::class;
        } else {
            $class = Pipeline::class;
        }

        if ($this->connection instanceof RelayConnection) {
            if (isset($options['atomic']) && $options['atomic']) {
                $class = RelayAtomic::class;
            } elseif (isset($options['fire-and-forget']) && $options['fire-and-forget']) {
                throw new NotSupportedException('The "relay" extension does not support fire-and-forget pipelines.');
            } else {
                $class = RelayPipeline::class;
            }
        }

        


        $pipeline = new $class($this);

        if (isset($callable)) {
            return $pipeline->execute($callable);
        }

        return $pipeline;
    }

    







    public function transaction(...$arguments)
    {
        return $this->sharedContextFactory('createTransaction', func_get_args());
    }

    







    protected function createTransaction(?array $options = null, $callable = null)
    {
        $transaction = new MultiExecTransaction($this, $options);

        if (isset($callable)) {
            return $transaction->execute($callable);
        }

        return $transaction;
    }

    







    public function pubSubLoop(...$arguments)
    {
        return $this->sharedContextFactory('createPubSub', func_get_args());
    }

    





    public function push(?callable $preLoopCallback = null): PushConsumer
    {
        return new PushConsumer($this, $preLoopCallback);
    }

    







    protected function createPubSub(?array $options = null, $callable = null)
    {
        if ($this->connection instanceof RelayConnection) {
            $pubsub = new RelayPubSubConsumer($this, $options);
        } else {
            $pubsub = new PubSubConsumer($this, $options);
        }

        if (!isset($callable)) {
            return $pubsub;
        }

        foreach ($pubsub as $message) {
            if (call_user_func($callable, $pubsub, $message) === false) {
                $pubsub->stop();
            }
        }

        return null;
    }

    




    public function monitor()
    {
        return new MonitorConsumer($this);
    }

    


    #[ReturnTypeWillChange]
    public function getIterator()
    {
        $clients = [];
        $connection = $this->getConnection();

        if (!$connection instanceof Traversable) {
            return new ArrayIterator([
                (string) $connection => new static($connection, $this->getOptions()),
            ]);
        }

        foreach ($connection as $node) {
            $clients[(string) $node] = new static($node, $this->getOptions());
        }

        return new ArrayIterator($clients);
    }
}
