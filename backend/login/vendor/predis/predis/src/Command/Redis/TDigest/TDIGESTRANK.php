<?php











namespace Predis\Command\Redis\TDigest;

use Predis\Command\PrefixableCommand as RedisCommand;








class TDIGESTRANK extends RedisCommand
{
    public function getId()
    {
        return 'TDIGEST.RANK';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
