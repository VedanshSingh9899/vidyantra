<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class ZREMRANGEBYRANK extends RedisCommand
{
    


    public function getId()
    {
        return 'ZREMRANGEBYRANK';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
