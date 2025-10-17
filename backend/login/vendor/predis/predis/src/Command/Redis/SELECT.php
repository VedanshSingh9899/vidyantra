<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class SELECT extends RedisCommand
{
    


    public function getId()
    {
        return 'SELECT';
    }
}
