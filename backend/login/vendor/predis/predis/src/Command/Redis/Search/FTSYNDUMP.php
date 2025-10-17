<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\PrefixableCommand as RedisCommand;






class FTSYNDUMP extends RedisCommand
{
    public function getId()
    {
        return 'FT.SYNDUMP';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
