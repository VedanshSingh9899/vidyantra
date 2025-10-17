<?php











namespace Predis\Command\Redis\BloomFilter;

use Predis\Command\PrefixableCommand as RedisCommand;






class BFLOADCHUNK extends RedisCommand
{
    public function getId()
    {
        return 'BF.LOADCHUNK';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
