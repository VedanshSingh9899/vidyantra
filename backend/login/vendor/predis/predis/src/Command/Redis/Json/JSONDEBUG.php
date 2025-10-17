<?php











namespace Predis\Command\Redis\Json;

use Predis\Command\Command as RedisCommand;






class JSONDEBUG extends RedisCommand
{
    public function getId()
    {
        return 'JSON.DEBUG';
    }
}
