<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class MONITOR extends RedisCommand
{
    


    public function getId()
    {
        return 'MONITOR';
    }
}
