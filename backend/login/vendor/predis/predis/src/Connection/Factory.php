<?php











namespace Predis\Connection;

use InvalidArgumentException;
use Predis\Client;
use Predis\Command\RawCommand;
use ReflectionClass;
use UnexpectedValueException;




class Factory implements FactoryInterface
{
    private $defaults = [];

    protected $schemes = [
        'tcp' => 'Predis\Connection\StreamConnection',
        'unix' => 'Predis\Connection\StreamConnection',
        'tls' => 'Predis\Connection\StreamConnection',
        'redis' => 'Predis\Connection\StreamConnection',
        'rediss' => 'Predis\Connection\StreamConnection',
    ];

    









    protected function checkInitializer($initializer)
    {
        if (is_callable($initializer)) {
            return $initializer;
        }

        $class = new ReflectionClass($initializer);

        if (!$class->isSubclassOf('Predis\Connection\NodeConnectionInterface')) {
            throw new InvalidArgumentException(
                'A connection initializer must be a valid connection class or a callable object.'
            );
        }

        return $initializer;
    }

    


    public function define($scheme, $initializer)
    {
        $this->schemes[$scheme] = $this->checkInitializer($initializer);
    }

    


    public function undefine($scheme)
    {
        unset($this->schemes[$scheme]);
    }

    


    public function create($parameters)
    {
        if (!$parameters instanceof ParametersInterface) {
            $parameters = $this->createParameters($parameters);
        }

        $scheme = $parameters->scheme;

        if (!isset($this->schemes[$scheme])) {
            throw new InvalidArgumentException("Unknown connection scheme: '$scheme'.");
        }

        $initializer = $this->schemes[$scheme];

        if (is_callable($initializer)) {
            $connection = call_user_func($initializer, $parameters, $this);
        } else {
            $connection = new $initializer($parameters);
            $this->prepareConnection($connection);
        }

        if (!$connection instanceof NodeConnectionInterface) {
            throw new UnexpectedValueException(
                'Objects returned by connection initializers must implement ' .
                "'Predis\Connection\NodeConnectionInterface'."
            );
        }

        return $connection;
    }

    







    public function setDefaultParameters(array $parameters)
    {
        $this->defaults = $parameters;
    }

    




    public function getDefaultParameters()
    {
        return $this->defaults;
    }

    






    protected function createParameters($parameters)
    {
        if (is_string($parameters)) {
            $parameters = Parameters::parse($parameters);
        } else {
            $parameters = $parameters ?: [];
        }

        if ($this->defaults) {
            $parameters += $this->defaults;
        }

        return new Parameters($parameters);
    }

    




    protected function prepareConnection(NodeConnectionInterface $connection)
    {
        $parameters = $connection->getParameters();

        if (!empty($parameters->password)) {
            $cmdAuthArgs = [$parameters->protocol, 'AUTH'];

            if (empty($parameters->username)) {
                $parameters->username = 'default';
            }

            array_push($cmdAuthArgs, $parameters->username, $parameters->password);
            array_push($cmdAuthArgs, 'SETNAME', 'predis');

            $connection->addConnectCommand(
                new RawCommand('HELLO', $cmdAuthArgs)
            );
        } else {
            $connection->addConnectCommand(
                new RawCommand('HELLO', [$parameters->protocol ?? 2, 'SETNAME', 'predis'])
            );
        }

        $connection->addConnectCommand(
            new RawCommand('CLIENT', ['SETINFO', 'LIB-NAME', 'predis'])
        );

        $connection->addConnectCommand(
            new RawCommand('CLIENT', ['SETINFO', 'LIB-VER', Client::VERSION])
        );

        if (isset($parameters->database) && strlen($parameters->database)) {
            $connection->addConnectCommand(
                new RawCommand('SELECT', [$parameters->database])
            );
        }
    }
}
