<?php











namespace Predis\Configuration\Option;

use Predis\Configuration\OptionInterface;
use Predis\Configuration\OptionsInterface;





class Exceptions implements OptionInterface
{
    


    public function filter(OptionsInterface $options, $value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    


    public function getDefault(OptionsInterface $options)
    {
        return true;
    }
}
