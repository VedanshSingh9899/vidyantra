<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class RPOPLPUSH extends RedisCommand
{
    


    public function getId()
    {
        return 'RPOPLPUSH';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForAllArguments($prefix);
    }
}
