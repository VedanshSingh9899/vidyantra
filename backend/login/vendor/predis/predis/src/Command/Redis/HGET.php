<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class HGET extends RedisCommand
{
    


    public function getId()
    {
        return 'HGET';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
