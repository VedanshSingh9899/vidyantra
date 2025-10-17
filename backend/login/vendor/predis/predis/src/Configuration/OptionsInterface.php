<?php











namespace Predis\Configuration;

use Predis\Command\Processor\ProcessorInterface;
use Predis\Connection\FactoryInterface;
use Predis\Connection\ParametersInterface;












interface OptionsInterface
{
    






    public function getDefault($option);

    






    public function defined($option);

    






    public function __isset($option);

    






    public function __get($option);

    







    public function __set($option, $value);
}
