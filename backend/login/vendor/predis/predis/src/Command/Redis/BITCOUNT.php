<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;
use Predis\Command\Traits\BitByte;






class BITCOUNT extends RedisCommand
{
    use BitByte;

    


    public function getId()
    {
        return 'BITCOUNT';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
