<?php











namespace Predis\Configuration\Option;

use InvalidArgumentException;
use Predis\Command\FactoryInterface;
use Predis\Command\RawFactory;
use Predis\Command\RedisFactory;
use Predis\Configuration\OptionInterface;
use Predis\Configuration\OptionsInterface;




class Commands implements OptionInterface
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
                '%s expects a valid command factory',
                static::class
            ));
        }
    }

    











    protected function createFactoryByArray(OptionsInterface $options, array $value)
    {
        


        $commands = $this->getDefault($options);

        foreach ($value as $commandID => $commandClass) {
            if ($commandClass === null) {
                $commands->undefine($commandID);
            } else {
                $commands->define($commandID, $commandClass);
            }
        }

        return $commands;
    }

    















    protected function createFactoryByString(OptionsInterface $options, string $value)
    {
        switch (strtolower($value)) {
            case 'default':
            case 'predis':
                return $this->getDefault($options);

            case 'raw':
                return $this->createRawFactory($options);

            default:
                throw new InvalidArgumentException(sprintf(
                    '%s does not recognize `%s` as a supported configuration string',
                    static::class,
                    $value
                ));
        }
    }

    




    protected function createRawFactory(OptionsInterface $options): FactoryInterface
    {
        $commands = new RawFactory();

        if (isset($options->prefix)) {
            throw new InvalidArgumentException(sprintf(
                '%s does not support key prefixing', RawFactory::class
            ));
        }

        return $commands;
    }

    


    public function getDefault(OptionsInterface $options)
    {
        $commands = new RedisFactory();

        if (isset($options->prefix)) {
            $commands->setProcessor($options->prefix);
        }

        return $commands;
    }
}
