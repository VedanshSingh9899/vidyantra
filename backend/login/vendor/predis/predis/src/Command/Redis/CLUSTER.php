<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class CLUSTER extends RedisCommand
{
    public function getId()
    {
        return 'CLUSTER';
    }
}
