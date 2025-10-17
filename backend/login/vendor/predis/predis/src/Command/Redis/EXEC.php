<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class EXEC extends RedisCommand
{
    


    public function getId()
    {
        return 'EXEC';
    }
}
