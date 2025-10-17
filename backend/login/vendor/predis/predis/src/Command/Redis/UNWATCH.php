<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class UNWATCH extends RedisCommand
{
    


    public function getId()
    {
        return 'UNWATCH';
    }
}
