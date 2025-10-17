<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class DISCARD extends RedisCommand
{
    


    public function getId()
    {
        return 'DISCARD';
    }
}
