<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;
use Predis\Command\Traits\Expire\ExpireOptions;








class EXPIRE extends RedisCommand
{
    use ExpireOptions;

    


    public function getId()
    {
        return 'EXPIRE';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
