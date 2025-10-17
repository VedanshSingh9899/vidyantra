<?php











namespace Predis\Configuration\Option;

use InvalidArgumentException;
use Predis\Configuration\OptionInterface;
use Predis\Configuration\OptionsInterface;
use Predis\Connection\AggregateConnectionInterface;
use Predis\Connection\NodeConnectionInterface;










class Aggregate implements OptionInterface
{
    


    public function filter(OptionsInterface $options, $value)
    {
        if (!is_callable($value)) {
            throw new InvalidArgumentException(sprintf(
                '%s expects a callable object acting as an aggregate connection initializer',
                static::class
            ));
        }

        return $this->getConnectionInitializer($options, $value);
    }

    





















    protected function getConnectionInitializer(OptionsInterface $options, callable $callable)
    {
        return function ($parameters = null, $autoaggregate = false) use ($callable, $options) {
            $connection = call_user_func_array($callable, [&$parameters, $options, $this]);

            if (!$connection instanceof AggregateConnectionInterface) {
                throw new InvalidArgumentException(sprintf(
                    '%s expects the supplied callable to return an instance of %s, but %s was returned',
                    static::class,
                    AggregateConnectionInterface::class,
                    is_object($connection) ? get_class($connection) : gettype($connection)
                ));
            }

            if ($parameters && $autoaggregate) {
                static::aggregate($options, $connection, $parameters);
            }

            return $connection;
        };
    }

    






    public static function aggregate(OptionsInterface $options, AggregateConnectionInterface $connection, array $nodes)
    {
        $connections = $options->connections;

        foreach ($nodes as $node) {
            $connection->add($node instanceof NodeConnectionInterface ? $node : $connections->create($node));
        }
    }

    


    public function getDefault(OptionsInterface $options)
    {
        return;
    }
}
