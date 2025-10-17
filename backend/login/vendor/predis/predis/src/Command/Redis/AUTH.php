<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class AUTH extends RedisCommand
{
    


    public function getId()
    {
        return 'AUTH';
    }
}
