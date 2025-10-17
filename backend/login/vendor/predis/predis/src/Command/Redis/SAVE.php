<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class SAVE extends RedisCommand
{
    


    public function getId()
    {
        return 'SAVE';
    }
}
