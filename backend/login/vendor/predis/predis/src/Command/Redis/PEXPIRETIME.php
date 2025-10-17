<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;







class PEXPIRETIME extends RedisCommand
{
    public function getId()
    {
        return 'PEXPIRETIME';
    }
}
