<?php











namespace Predis\Command\Redis\TDigest;

use Predis\Command\PrefixableCommand as RedisCommand;








class TDIGESTREVRANK extends RedisCommand
{
    public function getId()
    {
        return 'TDIGEST.REVRANK';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
