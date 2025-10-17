<?php











namespace Predis\Command\Redis\CuckooFilter;

use Predis\Command\PrefixableCommand as RedisCommand;







class CFSCANDUMP extends RedisCommand
{
    public function getId()
    {
        return 'CF.SCANDUMP';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
