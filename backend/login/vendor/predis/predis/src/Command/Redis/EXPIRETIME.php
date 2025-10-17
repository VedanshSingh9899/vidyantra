<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;







class EXPIRETIME extends RedisCommand
{
    public function getId()
    {
        return 'EXPIRETIME';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
