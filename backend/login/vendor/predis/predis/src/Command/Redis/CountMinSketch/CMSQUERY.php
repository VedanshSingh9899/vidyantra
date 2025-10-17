<?php











namespace Predis\Command\Redis\CountMinSketch;

use Predis\Command\PrefixableCommand as RedisCommand;






class CMSQUERY extends RedisCommand
{
    public function getId()
    {
        return 'CMS.QUERY';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
