<?php











namespace Predis\Configuration\Option;

use InvalidArgumentException;
use Predis\Cluster\RedisStrategy;
use Predis\Configuration\OptionsInterface;
use Predis\Connection\Cluster\PredisCluster;
use Predis\Connection\Cluster\RedisCluster;
use Predis\Connection\Parameters;






class Cluster extends Aggregate
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
            case 'redis':
            case 'redis-cluster':
                return static function ($parameters, $options, $option) {
                    $optionParameters = $options->parameters ?? [];

                    return new RedisCluster(
                        $options->connections,
                        new Parameters($optionParameters),
                        new RedisStrategy($options->crc16),
                        $options->readTimeout
                    );
                };

            case 'predis':
                return $this->getDefaultConnectionInitializer();

            default:
                throw new InvalidArgumentException(sprintf(
                    '%s expects either `predis`, `redis` or `redis-cluster` as valid string values, `%s` given',
                    static::class,
                    $description
                ));
        }
    }

    




    protected function getDefaultConnectionInitializer()
    {
        return static function ($parameters, $options, $option) {
            $optionsParameters = $options->parameters ?? [];

            return new PredisCluster(new Parameters($optionsParameters));
        };
    }

    


    public function getDefault(OptionsInterface $options)
    {
        return $this->getConnectionInitializer(
            $options,
            $this->getDefaultConnectionInitializer()
        );
    }
}
