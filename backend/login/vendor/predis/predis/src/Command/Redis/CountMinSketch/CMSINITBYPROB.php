<?php











namespace Predis\Command\Redis\CountMinSketch;

use Predis\Command\PrefixableCommand as RedisCommand;






class CMSINITBYPROB extends RedisCommand
{
    public function getId()
    {
        return 'CMS.INITBYPROB';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
