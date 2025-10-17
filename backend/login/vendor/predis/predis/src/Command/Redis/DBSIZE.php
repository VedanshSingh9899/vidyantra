<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class DBSIZE extends RedisCommand
{
    


    public function getId()
    {
        return 'DBSIZE';
    }
}
