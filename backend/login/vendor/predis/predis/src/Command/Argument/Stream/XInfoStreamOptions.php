<?php











namespace Predis\Command\Argument\Stream;

use Predis\Command\Argument\ArrayableArgument;

class XInfoStreamOptions implements ArrayableArgument
{
    


    protected $options = [];

    






    public function full(?int $count = null): self
    {
        $this->options[] = 'FULL';

        if (null !== $count) {
            array_push($this->options, 'COUNT', $count);
        }

        return $this;
    }

    


    public function toArray(): array
    {
        return $this->options;
    }
}
