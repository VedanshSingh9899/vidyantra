<?php











namespace Predis\Command\Argument\Search;

class SugAddArguments extends CommonArguments
{
    




    public function incr(): self
    {
        $this->arguments[] = 'INCR';

        return $this;
    }
}
