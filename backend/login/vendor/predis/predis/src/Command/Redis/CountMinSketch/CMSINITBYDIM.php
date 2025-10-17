<?php











namespace Predis\Command\Redis\CountMinSketch;

use Predis\Command\PrefixableCommand as RedisCommand;






class CMSINITBYDIM extends RedisCommand
{
    public function getId()
    {
        return 'CMS.INITBYDIM';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
