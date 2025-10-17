<?php











namespace Predis\Command\Redis\CuckooFilter;

use Predis\Command\PrefixableCommand as RedisCommand;








class CFDEL extends RedisCommand
{
    public function getId()
    {
        return 'CF.DEL';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
