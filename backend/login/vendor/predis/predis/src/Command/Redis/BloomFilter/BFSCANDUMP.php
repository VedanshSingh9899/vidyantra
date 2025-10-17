<?php











namespace Predis\Command\Redis\BloomFilter;

use Predis\Command\PrefixableCommand as RedisCommand;







class BFSCANDUMP extends RedisCommand
{
    public function getId()
    {
        return 'BF.SCANDUMP';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
