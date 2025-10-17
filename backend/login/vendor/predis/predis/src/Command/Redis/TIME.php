<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class TIME extends RedisCommand
{
    


    public function getId()
    {
        return 'TIME';
    }
}
