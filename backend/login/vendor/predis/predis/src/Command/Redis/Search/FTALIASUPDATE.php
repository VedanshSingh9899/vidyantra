<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\PrefixableCommand as RedisCommand;







class FTALIASUPDATE extends RedisCommand
{
    public function getId()
    {
        return 'FT.ALIASUPDATE';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForAllArguments($prefix);
    }
}
