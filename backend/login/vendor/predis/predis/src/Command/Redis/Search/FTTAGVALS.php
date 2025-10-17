<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\PrefixableCommand as RedisCommand;






class FTTAGVALS extends RedisCommand
{
    public function getId()
    {
        return 'FT.TAGVALS';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
