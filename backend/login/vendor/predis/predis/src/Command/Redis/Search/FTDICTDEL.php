<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\PrefixableCommand as RedisCommand;






class FTDICTDEL extends RedisCommand
{
    public function getId()
    {
        return 'FT.DICTDEL';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
