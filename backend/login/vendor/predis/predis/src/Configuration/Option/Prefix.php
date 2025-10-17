<?php











namespace Predis\Configuration\Option;

use Predis\Command\Processor\KeyPrefixProcessor;
use Predis\Command\Processor\ProcessorInterface;
use Predis\Configuration\OptionInterface;
use Predis\Configuration\OptionsInterface;





class Prefix implements OptionInterface
{
    


    public function filter(OptionsInterface $options, $value)
    {
        if (is_callable($value)) {
            $value = call_user_func($value, $options);
        }

        if ($value instanceof ProcessorInterface) {
            return $value;
        }

        return new KeyPrefixProcessor((string) $value);
    }

    


    public function getDefault(OptionsInterface $options)
    {
        
    }
}
