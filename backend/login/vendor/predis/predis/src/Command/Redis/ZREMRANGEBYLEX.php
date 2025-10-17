<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class ZREMRANGEBYLEX extends RedisCommand
{
    


    public function getId()
    {
        return 'ZREMRANGEBYLEX';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
