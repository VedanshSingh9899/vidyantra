<?php











namespace Predis\Command\Redis\TimeSeries;

use Predis\Command\PrefixableCommand as RedisCommand;






class TSGET extends RedisCommand
{
    public function getId()
    {
        return 'TS.GET';
    }

    public function setArguments(array $arguments)
    {
        [$key] = $arguments;
        $commandArguments = (!empty($arguments[1])) ? $arguments[1]->toArray() : [];

        parent::setArguments(array_merge(
            [$key],
            $commandArguments
        ));
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
