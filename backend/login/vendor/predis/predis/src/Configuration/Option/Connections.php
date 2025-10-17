<?php











namespace Predis\Configuration\Option;

use InvalidArgumentException;
use Predis\Configuration\OptionInterface;
use Predis\Configuration\OptionsInterface;
use Predis\Connection\Factory;
use Predis\Connection\FactoryInterface;
use Predis\Connection\RelayConnection;
use Predis\Connection\RelayFactory;








class Connections implements OptionInterface
{
    


    public function filter(OptionsInterface $options, $value)
    {
        if (is_callable($value)) {
            $value = call_user_func($value, $options);
        }

        if ($value instanceof FactoryInterface) {
            return $value;
        } elseif (is_array($value)) {
            return $this->createFactoryByArray($options, $value);
        } elseif (is_string($value)) {
            return $this->createFactoryByString($options, $value);
        } else {
            throw new InvalidArgumentException(sprintf(
                '%s expects a valid connection factory', static::class
            ));
        }
    }

    













    protected function createFactoryByArray(OptionsInterface $options, array $value)
    {
        


        $factory = $this->getDefault($options);

        foreach ($value as $scheme => $initializer) {
            $factory->define($scheme, $initializer);
        }

        return $factory;
    }

    













    protected function createFactoryByString(OptionsInterface $options, string $value)
    {
        switch (strtolower($value)) {
            case 'relay':
                return $this->getRelayFactory($options);

            case 'default':
                return $this->getDefault($options);

            default:
                throw new InvalidArgumentException(sprintf(
                    '%s does not recognize `%s` as a supported configuration string', static::class, $value
                ));
        }
    }

    


    public function getDefault(OptionsInterface $options)
    {
        $factory = new Factory();

        if ($options->defined('parameters')) {
            $factory->setDefaultParameters($options->parameters);
        }

        return $factory;
    }

    





    private function getRelayFactory(OptionsInterface $options): FactoryInterface
    {
        $factory = new RelayFactory();

        if ($options->defined('parameters')) {
            $factory->setDefaultParameters($options->parameters);
        }

        return $factory;
    }
}
