<?php











namespace Predis\Connection;

use InvalidArgumentException;
use Predis\ClientException;
use Predis\Command\CommandInterface;
use Predis\NotSupportedException;
use Predis\Response\ErrorInterface as ErrorResponseInterface;
use Predis\Response\ServerException;
use Relay\Exception as RelayException;
use Relay\Relay;






























class RelayConnection extends AbstractConnection
{
    use RelayMethods;

    




    protected $client;

    




    public $atypicalCommands = [
        'AUTH',
        'SELECT',

        'TYPE',

        'MULTI',
        'EXEC',
        'DISCARD',

        'WATCH',
        'UNWATCH',

        'SUBSCRIBE',
        'UNSUBSCRIBE',
        'PSUBSCRIBE',
        'PUNSUBSCRIBE',
        'SSUBSCRIBE',
        'SUNSUBSCRIBE',
    ];

    


    public function __construct(ParametersInterface $parameters, Relay $client)
    {
        $this->assertExtensions();

        $this->parameters = $this->assertParameters($parameters);
        $this->client = $client;
    }

    


    public function isConnected()
    {
        return $this->client->isConnected();
    }

    


    public function disconnect()
    {
        if ($this->client->isConnected()) {
            $this->client->close();
        }
    }

    


    private function assertExtensions()
    {
        if (!extension_loaded('relay')) {
            throw new NotSupportedException(
                'The "relay" extension is required by this connection backend.'
            );
        }
    }

    




    private function createClient()
    {
        $client = new Relay();

        
        $client->setOption(Relay::OPT_PHPREDIS_COMPATIBILITY, false);

        
        $client->setOption(Relay::OPT_REPLY_LITERAL, true);

        
        $client->setOption(Relay::OPT_MAX_RETRIES, 0);

        
        $client->setOption(Relay::OPT_USE_CACHE, $this->parameters->cache ?? true);

        
        $client->setOption(Relay::OPT_SERIALIZER, constant(sprintf(
            '%s::SERIALIZER_%s',
            Relay::class,
            strtoupper($this->parameters->serializer ?? 'none')
        )));

        
        $client->setOption(Relay::OPT_COMPRESSION, constant(sprintf(
            '%s::COMPRESSION_%s',
            Relay::class,
            strtoupper($this->parameters->compression ?? 'none')
        )));

        return $client;
    }

    




    public function getClient()
    {
        return $this->client;
    }

    





    protected function connectWithConfiguration(ParametersInterface $parameters, $address, $flags)
    {
        $timeout = isset($parameters->timeout) ? (float) $parameters->timeout : 5.0;

        $retry_interval = 0;
        $read_timeout = 5.0;

        if (isset($parameters->read_write_timeout)) {
            $read_timeout = (float) $parameters->read_write_timeout;
            $read_timeout = $read_timeout > 0 ? $read_timeout : 0;
        }

        try {
            $this->client->connect(
                $parameters->path ?? $parameters->host,
                isset($parameters->path) ? 0 : $parameters->port,
                $timeout,
                null,
                $retry_interval,
                $read_timeout
            );
        } catch (RelayException $ex) {
            $this->onConnectionError($ex->getMessage(), $ex->getCode());
        }

        return $this->client;
    }

    


    public function getIdentifier()
    {
        try {
            return $this->client->endpointId();
        } catch (RelayException $ex) {
            return parent::getIdentifier();
        }
    }

    


    public function executeCommand(CommandInterface $command)
    {
        if (!$this->client->isConnected()) {
            $this->getResource();
        }

        try {
            $name = $command->getId();

            
            
            
            return in_array($name, $this->atypicalCommands)
                ? $this->client->{$name}(...$command->getArguments())
                : $this->client->rawCommand($name, ...$command->getArguments());
        } catch (RelayException $ex) {
            $exception = $this->onCommandError($ex, $command);

            if ($exception instanceof ErrorResponseInterface) {
                return $exception;
            }

            throw $exception;
        }
    }

    


    public function onCommandError(RelayException $exception, CommandInterface $command)
    {
        $code = $exception->getCode();
        $message = $exception->getMessage();

        if (strpos($message, 'RELAY_ERR_IO') !== false) {
            return new ConnectionException($this, $message, $code, $exception);
        }

        if (strpos($message, 'RELAY_ERR_REDIS') !== false) {
            return new ServerException($message, $code, $exception);
        }

        if (strpos($message, 'RELAY_ERR_WRONGTYPE') !== false && strpos($message, "Got reply-type 'status'") !== false) {
            $message = 'Operation against a key holding the wrong kind of value';
        }

        return new ClientException($message, $code, $exception);
    }

    





    public function pack($value)
    {
        return $this->client->_pack($value);
    }

    





    public function unpack($value)
    {
        return $this->client->_unpack($value);
    }

    


    public function writeRequest(CommandInterface $command)
    {
        throw new NotSupportedException('The "relay" extension does not support writing requests.');
    }

    


    public function readResponse(CommandInterface $command)
    {
        throw new NotSupportedException('The "relay" extension does not support reading responses.');
    }

    


    public function __destruct()
    {
        $this->disconnect();
    }

    


    protected function createResource()
    {
        switch ($this->parameters->scheme) {
            case 'tcp':
            case 'redis':
                return $this->initializeTcpConnection($this->parameters);

            case 'unix':
                return $this->initializeUnixConnection($this->parameters);

            default:
                throw new InvalidArgumentException("Invalid scheme: '{$this->parameters->scheme}'.");
        }
    }

    






    protected function initializeTcpConnection(ParametersInterface $parameters)
    {
        if (!filter_var($parameters->host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $address = "tcp://$parameters->host:$parameters->port";
        } else {
            $address = "tcp://[$parameters->host]:$parameters->port";
        }

        $flags = STREAM_CLIENT_CONNECT;

        if (isset($parameters->async_connect) && $parameters->async_connect) {
            $flags |= STREAM_CLIENT_ASYNC_CONNECT;
        }

        if (isset($parameters->persistent)) {
            if (false !== $persistent = filter_var($parameters->persistent, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
                $flags |= STREAM_CLIENT_PERSISTENT;

                if ($persistent === null) {
                    $address = "{$address}/{$parameters->persistent}";
                }
            }
        }

        return $this->connectWithConfiguration($parameters, $address, $flags);
    }

    






    protected function initializeUnixConnection(ParametersInterface $parameters)
    {
        if (!isset($parameters->path)) {
            throw new InvalidArgumentException('Missing UNIX domain socket path.');
        }

        $flags = STREAM_CLIENT_CONNECT;

        if (isset($parameters->persistent)) {
            if (false !== $persistent = filter_var($parameters->persistent, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
                $flags |= STREAM_CLIENT_PERSISTENT;

                if ($persistent === null) {
                    throw new InvalidArgumentException(
                        'Persistent connection IDs are not supported when using UNIX domain sockets.'
                    );
                }
            }
        }

        return $this->connectWithConfiguration($parameters, "unix://{$parameters->path}", $flags);
    }

    


    public function connect()
    {
        if (parent::connect() && $this->initCommands) {
            foreach ($this->initCommands as $command) {
                $response = $this->executeCommand($command);

                if ($response instanceof ErrorResponseInterface && ($command->getId() === 'CLIENT')) {
                    
                } elseif ($response instanceof ErrorResponseInterface) {
                    $this->onConnectionError("`{$command->getId()}` failed: {$response->getMessage()}", 0);
                }
            }
        }
    }

    


    public function read()
    {
        throw new NotSupportedException('The "relay" extension does not support reading responses.');
    }

    


    protected function assertParameters(ParametersInterface $parameters)
    {
        if (!in_array($parameters->scheme, ['tcp', 'tls', 'unix', 'redis', 'rediss'])) {
            throw new InvalidArgumentException("Invalid scheme: '{$parameters->scheme}'.");
        }

        if (!in_array($parameters->serializer, [null, 'php', 'igbinary', 'msgpack', 'json'])) {
            throw new InvalidArgumentException("Invalid serializer: '{$parameters->serializer}'.");
        }

        if (!in_array($parameters->compression, [null, 'lzf', 'lz4', 'zstd'])) {
            throw new InvalidArgumentException("Invalid compression algorithm: '{$parameters->compression}'.");
        }

        return $parameters;
    }

    


    public function write(string $buffer): void
    {
        throw new NotSupportedException('The "relay" extension does not support writing operations.');
    }
}
