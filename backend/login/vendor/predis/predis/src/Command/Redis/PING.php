<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class PING extends RedisCommand
{
    


    public function getId()
    {
        return 'PING';
    }
}
