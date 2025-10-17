<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class ZREMRANGEBYSCORE extends RedisCommand
{
    


    public function getId()
    {
        return 'ZREMRANGEBYSCORE';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
