<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class FLUSHALL extends RedisCommand
{
    


    public function getId()
    {
        return 'FLUSHALL';
    }
}
