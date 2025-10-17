<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class MULTI extends RedisCommand
{
    


    public function getId()
    {
        return 'MULTI';
    }
}
