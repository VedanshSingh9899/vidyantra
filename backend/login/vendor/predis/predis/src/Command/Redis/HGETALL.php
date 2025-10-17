<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class HGETALL extends RedisCommand
{
    


    public function getId()
    {
        return 'HGETALL';
    }

    


    public function parseResponse($data)
    {
        if ($data !== array_values($data)) {
            return $data; 
        }

        $result = [];

        for ($i = 0; $i < count($data); ++$i) {
            $result[$data[$i]] = $data[++$i];
        }

        return $result;
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
