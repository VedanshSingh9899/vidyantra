<?php











namespace Predis\Configuration\Option;

use InvalidArgumentException;
use Predis\Configuration\OptionsInterface;
use Predis\Connection\AggregateConnectionInterface;
use Predis\Connection\Replication\MasterSlaveReplication;
use Predis\Connection\Replication\SentinelReplication;





class Replication extends Aggregate
{
    


    public function filter(OptionsInterface $options, $value)
    {
        if (is_string($value)) {
            $value = $this->getConnectionInitializerByString($options, $value);
        }

        if (is_callable($value)) {
            return $this->getConnectionInitializer($options, $value);
        } else {
            throw new InvalidArgumentException(sprintf(
                '%s expects either a string or a callable value, %s given',
                static::class,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }
    }

    


















    protected function getConnectionInitializerByString(OptionsInterface $options, string $description)
    {
        switch ($description) {
            case 'sentinel':
            case 'redis-sentinel':
                return function ($parameters, $options) {
                    return new SentinelReplication($options->service, $parameters, $options->connections);
                };

            case 'predis':
                return $this->getDefaultConnectionInitializer();

            default:
                throw new InvalidArgumentException(sprintf(
                    '%s expects either `predis`, `sentinel` or `redis-sentinel` as valid string values, `%s` given',
                    static::class,
                    $description
                ));
        }
    }

    




    protected function getDefaultConnectionInitializer()
    {
        return function ($parameters, $options) {
            $connection = new MasterSlaveReplication();

            if ($options->autodiscovery) {
                $connection->setConnectionFactory($options->connections);
                $connection->setAutoDiscovery(true);
            }

            return $connection;
        };
    }

    


    public static function aggregate(OptionsInterface $options, AggregateConnectionInterface $connection, array $nodes)
    {
        if (!$connection instanceof SentinelReplication) {
            parent::aggregate($options, $connection, $nodes);
        }
    }

    


    public function getDefault(OptionsInterface $options)
    {
        return $this->getConnectionInitializer(
            $options,
            $this->getDefaultConnectionInitializer()
        );
    }
}
