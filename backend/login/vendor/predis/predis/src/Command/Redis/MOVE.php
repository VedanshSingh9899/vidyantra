<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class MOVE extends RedisCommand
{
    


    public function getId()
    {
        return 'MOVE';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
