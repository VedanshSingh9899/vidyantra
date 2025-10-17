<?php











namespace Predis\Command\Redis\TopK;

use Predis\Command\PrefixableCommand as RedisCommand;







class TOPKQUERY extends RedisCommand
{
    public function getId()
    {
        return 'TOPK.QUERY';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
