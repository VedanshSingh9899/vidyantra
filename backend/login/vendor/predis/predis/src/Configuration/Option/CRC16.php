<?php











namespace Predis\Configuration\Option;

use InvalidArgumentException;
use Predis\Cluster\Hash;
use Predis\Configuration\OptionInterface;
use Predis\Configuration\OptionsInterface;




class CRC16 implements OptionInterface
{
    







    protected function getHashGeneratorByDescription(OptionsInterface $options, $description)
    {
        if ($description === 'predis') {
            return new Hash\CRC16();
        } else {
            throw new InvalidArgumentException(
                'String value for the crc16 option must be either `predis`'
            );
        }
    }

    


    public function filter(OptionsInterface $options, $value)
    {
        if (is_callable($value)) {
            $value = call_user_func($value, $options);
        }

        if (is_string($value)) {
            return $this->getHashGeneratorByDescription($options, $value);
        } elseif ($value instanceof Hash\HashGeneratorInterface) {
            return $value;
        } else {
            $class = get_class($this);
            throw new InvalidArgumentException("$class expects a valid hash generator");
        }
    }

    


    public function getDefault(OptionsInterface $options)
    {
        return new Hash\CRC16();
    }
}
