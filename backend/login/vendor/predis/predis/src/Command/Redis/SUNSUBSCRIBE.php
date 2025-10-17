<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;






class SUNSUBSCRIBE extends RedisCommand
{
    public function getId()
    {
        return 'SUNSUBSCRIBE';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForAllArguments($prefix);
    }
}
