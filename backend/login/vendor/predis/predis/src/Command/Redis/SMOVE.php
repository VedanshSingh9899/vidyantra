<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class SMOVE extends RedisCommand
{
    


    public function getId()
    {
        return 'SMOVE';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixSkippingLastArgument($prefix);
    }
}
