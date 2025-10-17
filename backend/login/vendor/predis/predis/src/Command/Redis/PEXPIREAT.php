<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class PEXPIREAT extends RedisCommand
{
    


    public function getId()
    {
        return 'PEXPIREAT';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
