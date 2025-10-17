<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\PrefixableCommand as RedisCommand;






class FTALIASADD extends RedisCommand
{
    public function getId()
    {
        return 'FT.ALIASADD';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForAllArguments($prefix);
    }
}
