<?php











namespace Predis\Command\Redis\Json;

use Predis\Command\PrefixableCommand as RedisCommand;







class JSONMERGE extends RedisCommand
{
    public function getId()
    {
        return 'JSON.MERGE';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
