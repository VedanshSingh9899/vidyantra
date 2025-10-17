<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;
use Predis\Command\Traits\BitByte;






class BITPOS extends RedisCommand
{
    use BitByte;

    


    public function getId()
    {
        return 'BITPOS';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
