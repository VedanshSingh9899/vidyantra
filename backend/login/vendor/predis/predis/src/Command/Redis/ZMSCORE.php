<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;









class ZMSCORE extends RedisCommand
{
    


    public function getId()
    {
        return 'ZMSCORE';
    }
}
