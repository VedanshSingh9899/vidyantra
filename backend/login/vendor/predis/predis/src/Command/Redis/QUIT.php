<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class QUIT extends RedisCommand
{
    


    public function getId()
    {
        return 'QUIT';
    }
}
