<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class BRPOPLPUSH extends RedisCommand
{
    


    public function getId()
    {
        return 'BRPOPLPUSH';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixSkippingLastArgument($prefix);
    }
}
