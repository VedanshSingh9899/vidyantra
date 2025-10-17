<?php











namespace Predis\Command\Redis\TopK;

use Predis\Command\PrefixableCommand as RedisCommand;









class TOPKADD extends RedisCommand
{
    public function getId()
    {
        return 'TOPK.ADD';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
