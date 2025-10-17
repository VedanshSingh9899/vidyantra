<?php











namespace Predis\Command\Redis\TimeSeries;

use Predis\Command\PrefixableCommand as RedisCommand;






class TSADD extends RedisCommand
{
    public function getId()
    {
        return 'TS.ADD';
    }

    public function setArguments(array $arguments)
    {
        [$key, $timestamp, $value] = $arguments;
        $commandArguments = (!empty($arguments[3])) ? $arguments[3]->toArray() : [];

        parent::setArguments(array_merge(
            [$key, $timestamp, $value],
            $commandArguments
        ));
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
