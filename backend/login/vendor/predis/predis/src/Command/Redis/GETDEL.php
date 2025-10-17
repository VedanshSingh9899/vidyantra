<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;

class GETDEL extends RedisCommand
{
    public function getId()
    {
        return 'GETDEL';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
