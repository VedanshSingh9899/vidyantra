<?php











namespace Predis\Command\Redis\CuckooFilter;

use Predis\Command\PrefixableCommand as RedisCommand;







class CFLOADCHUNK extends RedisCommand
{
    public function getId()
    {
        return 'CF.LOADCHUNK';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
