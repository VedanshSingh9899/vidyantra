<?php











namespace Predis\Command\Redis\CuckooFilter;

use Predis\Command\PrefixableCommand as RedisCommand;






class CFMEXISTS extends RedisCommand
{
    public function getId()
    {
        return 'CF.MEXISTS';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
