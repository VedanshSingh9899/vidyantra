<?php











namespace Predis\Command\Argument\Search;

use Predis\Command\Argument\ArrayableArgument;

class DropArguments implements ArrayableArgument
{
    


    protected $arguments = [];

    




    public function dd(): self
    {
        $this->arguments[] = 'DD';

        return $this;
    }

    


    public function toArray(): array
    {
        return $this->arguments;
    }
}
