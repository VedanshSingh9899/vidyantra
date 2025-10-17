<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class RANDOMKEY extends RedisCommand
{
    


    public function getId()
    {
        return 'RANDOMKEY';
    }

    


    public function parseResponse($data)
    {
        return $data !== '' ? $data : null;
    }
}
