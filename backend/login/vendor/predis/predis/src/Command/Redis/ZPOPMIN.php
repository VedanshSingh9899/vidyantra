<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class ZPOPMIN extends RedisCommand
{
    


    public function getId()
    {
        return 'ZPOPMIN';
    }

    


    public function parseResponse($data)
    {
        $result = [];

        for ($i = 0; $i < count($data); ++$i) {
            if (is_array($data[$i])) {
                $result[$data[$i][0]] = $data[$i][1]; 
            } else {
                $result[$data[$i]] = $data[++$i];
            }
        }

        return $result;
    }

    



    public function parseResp3Response($data)
    {
        $parsedData = [];

        foreach ($data as $element) {
            if (is_array($element)) {
                $parsedData[] = $this->parseResponse($element);
            } else {
                return $this->parseResponse($data);
            }
        }

        return $parsedData;
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
