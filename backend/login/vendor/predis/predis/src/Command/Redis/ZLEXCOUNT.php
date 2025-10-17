<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class ZLEXCOUNT extends RedisCommand
{
    


    public function getId()
    {
        return 'ZLEXCOUNT';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
