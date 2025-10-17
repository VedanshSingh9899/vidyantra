<?php











namespace Predis\Command\Redis\BloomFilter;

use Predis\Command\PrefixableCommand as RedisCommand;






class BFEXISTS extends RedisCommand
{
    public function getId()
    {
        return 'BF.EXISTS';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
