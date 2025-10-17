<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class EXISTS extends RedisCommand
{
    


    public function getId()
    {
        return 'EXISTS';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForAllArguments($prefix);
    }
}
