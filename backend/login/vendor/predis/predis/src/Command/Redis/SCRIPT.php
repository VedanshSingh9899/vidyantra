<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class SCRIPT extends RedisCommand
{
    


    public function getId()
    {
        return 'SCRIPT';
    }
}
