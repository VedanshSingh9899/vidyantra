<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class LASTSAVE extends RedisCommand
{
    


    public function getId()
    {
        return 'LASTSAVE';
    }
}
