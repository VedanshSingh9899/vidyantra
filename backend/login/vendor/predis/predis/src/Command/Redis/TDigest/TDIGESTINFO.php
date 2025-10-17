<?php











namespace Predis\Command\Redis\TDigest;

use Predis\Command\PrefixableCommand as RedisCommand;






class TDIGESTINFO extends RedisCommand
{
    public function getId()
    {
        return 'TDIGEST.INFO';
    }

    public function parseResponse($data)
    {
        $result = [];

        for ($i = 0, $iMax = count($data); $i < $iMax; ++$i) {
            if (array_key_exists($i + 1, $data)) {
                $result[(string) $data[$i]] = $data[++$i];
            }
        }

        return $result;
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
