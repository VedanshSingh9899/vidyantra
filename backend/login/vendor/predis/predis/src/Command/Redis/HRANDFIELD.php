<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;
use Predis\Command\Traits\With\WithValues;









class HRANDFIELD extends RedisCommand
{
    use WithValues;

    public function getId()
    {
        return 'HRANDFIELD';
    }

    


    public function parseResponse($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        
        $return = [];

        array_walk_recursive($data, function ($value) use (&$return) {
            $return[] = $value;
        });

        return $return;
    }
}
