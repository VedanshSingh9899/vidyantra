<?php











namespace Predis\Command\Argument\TimeSeries;

class RangeArguments extends CommonArguments
{
    





    public function filterByTs(int ...$ts): self
    {
        array_push($this->arguments, 'FILTER_BY_TS', ...$ts);

        return $this;
    }

    






    public function filterByValue(int $min, int $max): self
    {
        array_push($this->arguments, 'FILTER_BY_VALUE', $min, $max);

        return $this;
    }

    





    public function count(int $count): self
    {
        array_push($this->arguments, 'COUNT', $count);

        return $this;
    }

    









    public function aggregation(string $aggregator, int $bucketDuration, int $align = 0, int $bucketTimestamp = 0, bool $empty = false): self
    {
        if ($align > 0) {
            array_push($this->arguments, 'ALIGN', $align);
        }

        array_push($this->arguments, 'AGGREGATION', $aggregator, $bucketDuration);

        if ($bucketTimestamp > 0) {
            array_push($this->arguments, 'BUCKETTIMESTAMP', $bucketTimestamp);
        }

        if (true === $empty) {
            $this->arguments[] = 'EMPTY';
        }

        return $this;
    }
}
