<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;






class SPUBLISH extends RedisCommand
{
    public function getId()
    {
        return 'SPUBLISH';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
