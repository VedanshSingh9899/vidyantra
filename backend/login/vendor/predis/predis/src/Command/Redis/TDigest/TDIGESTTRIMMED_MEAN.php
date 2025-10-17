<?php











namespace Predis\Command\Redis\TDigest;

use Predis\Command\PrefixableCommand as RedisCommand;







class TDIGESTTRIMMED_MEAN extends RedisCommand
{
    public function getId()
    {
        return 'TDIGEST.TRIMMED_MEAN';
    }

    


    public function parseResponse($data)
    {
        if (is_string($data) || !is_float($data)) {
            return $data;
        }

        
        if (is_nan($data)) {
            return 'nan';
        }

        switch ($data) {
            case INF: return 'inf';
            case -INF: return '-inf';
            default: return $data;
        }
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
