<?php











namespace Predis\Command\Argument\TimeSeries;

class MRangeArguments extends RangeArguments
{
    





    public function filter(string ...$filterExpressions): self
    {
        array_push($this->arguments, 'FILTER', ...$filterExpressions);

        return $this;
    }

    







    public function groupBy(string $label, string $reducer): self
    {
        array_push($this->arguments, 'GROUPBY', $label, 'REDUCE', $reducer);

        return $this;
    }
}
