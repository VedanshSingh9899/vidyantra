<?php











namespace Predis\Command\Argument\TimeSeries;

class AddArguments extends CommonArguments
{
    






    public function onDuplicate(string $policy = self::POLICY_BLOCK): self
    {
        array_push($this->arguments, 'ON_DUPLICATE', $policy);

        return $this;
    }
}
