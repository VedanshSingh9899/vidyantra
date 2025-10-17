<?php











namespace Predis\Command\Argument\Search;

class SugGetArguments extends CommonArguments
{
    




    public function fuzzy(): self
    {
        $this->arguments[] = 'FUZZY';

        return $this;
    }

    





    public function max(int $num): self
    {
        array_push($this->arguments, 'MAX', $num);

        return $this;
    }
}
