<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;







class CONFIG extends RedisCommand
{
    


    public function getId()
    {
        return 'CONFIG';
    }

    


    public function parseResponse($data)
    {
        if (is_array($data)) {
            if ($data !== array_values($data)) {
                return $data; 
            }

            $result = [];

            for ($i = 0; $i < count($data); ++$i) {
                $result[$data[$i]] = $data[++$i];
            }

            return $result;
        }

        return $data;
    }
}
