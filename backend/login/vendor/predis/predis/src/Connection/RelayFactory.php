<?php











namespace Predis\Connection;

use InvalidArgumentException;
use Predis\Command\RawCommand;
use Predis\NotSupportedException;
use Relay\Relay;

class RelayFactory extends Factory
{
    


    protected $schemes = [
        'tcp' => RelayConnection::class,
        'tls' => RelayConnection::class,
        'unix' => RelayConnection::class,
        'redis' => RelayConnection::class,
        'rediss' => RelayConnection::class,
    ];

    


    public function define($scheme, $initializer)
    {
        throw new NotSupportedException('Does not allow to override existing initializer.');
    }

    


    public function undefine($scheme)
    {
        throw new NotSupportedException('Does not allow to override existing initializer.');
    }

    


    public function create($parameters): NodeConnectionInterface
    {
        $this->assertExtensions();

        if (!$parameters instanceof ParametersInterface) {
            $parameters = $this->createParameters($parameters);
        }

        $scheme = $parameters->scheme;

        if (!isset($this->schemes[$scheme])) {
            throw new InvalidArgumentException("Unknown connection scheme: '$scheme'.");
        }

        $initializer = $this->schemes[$scheme];
        $client = $this->createClient();

        $connection = new $initializer($parameters, $client);

        $this->prepareConnection($connection);

        return $connection;
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

    


    protected function prepareConnection(NodeConnectionInterface $connection)
    {
        $parameters = $connection->getParameters();

        if (isset($parameters->password) && strlen($parameters->password)) {
            $cmdAuthArgs = isset($parameters->username) && strlen($parameters->username)
                ? [$parameters->username, $parameters->password]
                : [$parameters->password];

            $connection->addConnectCommand(
                new RawCommand('AUTH', $cmdAuthArgs)
            );
        }

        if (isset($parameters->database) && strlen($parameters->database)) {
            $connection->addConnectCommand(
                new RawCommand('SELECT', [$parameters->database])
            );
        }
    }
}
