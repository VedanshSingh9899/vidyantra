<?php











namespace Predis\Command\Redis\TopK;

use Predis\Command\PrefixableCommand as RedisCommand;








class TOPKINCRBY extends RedisCommand
{
    public function getId()
    {
        return 'TOPK.INCRBY';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
