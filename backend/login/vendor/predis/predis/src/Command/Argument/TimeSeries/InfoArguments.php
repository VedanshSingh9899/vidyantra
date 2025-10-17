<?php











namespace Predis\Command\Argument\TimeSeries;

use Predis\Command\Argument\ArrayableArgument;

class InfoArguments implements ArrayableArgument
{
    


    private $arguments = [];

    




    public function debug(): self
    {
        $this->arguments[] = 'DEBUG';

        return $this;
    }

    


    public function toArray(): array
    {
        return $this->arguments;
    }
}
