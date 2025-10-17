<?php











namespace Predis\Command\Redis\TDigest;

use Predis\Command\PrefixableCommand as RedisCommand;






class TDIGESTADD extends RedisCommand
{
    public function getId()
    {
        return 'TDIGEST.ADD';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
