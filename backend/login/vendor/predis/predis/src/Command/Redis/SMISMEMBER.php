<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;






class SMISMEMBER extends RedisCommand
{
    public function getId()
    {
        return 'SMISMEMBER';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
