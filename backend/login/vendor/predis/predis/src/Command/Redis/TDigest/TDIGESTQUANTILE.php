<?php











namespace Predis\Command\Redis\TDigest;

use Predis\Command\PrefixableCommand as RedisCommand;






class TDIGESTQUANTILE extends RedisCommand
{
    public function getId()
    {
        return 'TDIGEST.QUANTILE';
    }

    


    public function parseResponse($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        
        return array_map(function ($value) {
            if (is_string($value) || !is_float($value)) {
                return $value;
            }

            if (is_nan($value)) {
                return 'nan';
            }

            switch ($value) {
                case INF: return 'inf';
                case -INF: return '-inf';
                default: return $value;
            }
        }, $data);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
