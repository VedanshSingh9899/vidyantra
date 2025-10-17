<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class HINCRBYFLOAT extends RedisCommand
{
    


    public function getId()
    {
        return 'HINCRBYFLOAT';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
