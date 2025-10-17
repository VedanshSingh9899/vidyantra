<?php











namespace Predis\Command\Redis\CuckooFilter;

use Predis\Command\PrefixableCommand as RedisCommand;






class CFINFO extends RedisCommand
{
    public function getId()
    {
        return 'CF.INFO';
    }

    public function parseResponse($data)
    {
        if (count($data) > 1) {
            $result = [];

            for ($i = 0, $iMax = count($data); $i < $iMax; ++$i) {
                if (array_key_exists($i + 1, $data)) {
                    $result[(string) $data[$i]] = $data[++$i];
                }
            }

            return $result;
        }

        return $data;
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
