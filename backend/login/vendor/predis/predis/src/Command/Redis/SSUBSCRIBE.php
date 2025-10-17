<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;






class SSUBSCRIBE extends RedisCommand
{
    public function getId()
    {
        return 'SSUBSCRIBE';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForAllArguments($prefix);
    }
}
