<?php











namespace Predis\Command\Redis\TimeSeries;

use Predis\Command\PrefixableCommand as RedisCommand;






class TSDELETERULE extends RedisCommand
{
    public function getId()
    {
        return 'TS.DELETERULE';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
