<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class BGSAVE extends RedisCommand
{
    


    public function getId()
    {
        return 'BGSAVE';
    }

    


    public function parseResponse($data)
    {
        return $data === 'Background saving started' ? true : $data;
    }
}
