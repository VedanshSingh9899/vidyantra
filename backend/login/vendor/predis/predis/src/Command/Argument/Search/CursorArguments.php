<?php











namespace Predis\Command\Argument\Search;

use Predis\Command\Argument\ArrayableArgument;

class CursorArguments implements ArrayableArgument
{
    


    protected $arguments = [];

    





    public function count(int $readSize): self
    {
        array_push($this->arguments, 'COUNT', $readSize);

        return $this;
    }

    


    public function toArray(): array
    {
        return $this->arguments;
    }
}
