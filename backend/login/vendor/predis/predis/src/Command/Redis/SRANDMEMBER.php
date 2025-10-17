<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class SRANDMEMBER extends RedisCommand
{
    


    public function getId()
    {
        return 'SRANDMEMBER';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
