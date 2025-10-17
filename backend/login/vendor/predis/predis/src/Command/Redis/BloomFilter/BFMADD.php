<?php











namespace Predis\Command\Redis\BloomFilter;

use Predis\Command\PrefixableCommand as RedisCommand;







class BFMADD extends RedisCommand
{
    public function getId()
    {
        return 'BF.MADD';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
