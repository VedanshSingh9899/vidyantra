<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class INCRBYFLOAT extends RedisCommand
{
    


    public function getId()
    {
        return 'INCRBYFLOAT';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
