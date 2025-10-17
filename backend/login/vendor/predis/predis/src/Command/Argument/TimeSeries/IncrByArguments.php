<?php











namespace Predis\Command\Argument\TimeSeries;

class IncrByArguments extends CommonArguments
{
    





    public function timestamp($timeStamp): self
    {
        array_push($this->arguments, 'TIMESTAMP', $timeStamp);

        return $this;
    }

    




    public function uncompressed(): self
    {
        $this->arguments[] = 'UNCOMPRESSED';

        return $this;
    }
}
