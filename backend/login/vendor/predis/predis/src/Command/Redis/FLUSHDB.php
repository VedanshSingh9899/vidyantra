<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class FLUSHDB extends RedisCommand
{
    


    public function getId()
    {
        return 'FLUSHDB';
    }
}
