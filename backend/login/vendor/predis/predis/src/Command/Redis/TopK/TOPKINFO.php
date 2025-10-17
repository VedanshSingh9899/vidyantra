<?php











namespace Predis\Command\Redis\TopK;

use Predis\Command\PrefixableCommand as RedisCommand;






class TOPKINFO extends RedisCommand
{
    public function getId()
    {
        return 'TOPK.INFO';
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
