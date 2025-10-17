<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;







class ACL extends RedisCommand
{
    public function getId()
    {
        return 'ACL';
    }

    


    public function parseResponse($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        if ($data === array_values($data)) {
            return $data;
        }

        
        $return = [];

        array_walk($data, function ($value, $key) use (&$return) {
            $return[] = $key;
            $return[] = $value;
        });

        return $return;
    }
}
