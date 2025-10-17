<?php











namespace Predis\Command\Redis\CountMinSketch;

use Predis\Command\PrefixableCommand as RedisCommand;







class CMSINCRBY extends RedisCommand
{
    public function getId()
    {
        return 'CMS.INCRBY';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
