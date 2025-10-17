<?php











namespace Predis\Command\Redis\TimeSeries;

use Predis\Command\PrefixableCommand as RedisCommand;






class TSRANGE extends RedisCommand
{
    public function getId()
    {
        return 'TS.RANGE';
    }

    public function setArguments(array $arguments)
    {
        [$key, $fromTimestamp, $toTimestamp] = $arguments;
        $commandArguments = (!empty($arguments[3])) ? $arguments[3]->toArray() : [];

        parent::setArguments(array_merge(
            [$key, $fromTimestamp, $toTimestamp],
            $commandArguments
        ));
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
