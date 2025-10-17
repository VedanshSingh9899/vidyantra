<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;

class VREM extends RedisCommand
{
    


    public function getId(): string
    {
        return 'VREM';
    }

    



    public function parseResponse($data): bool
    {
        return (bool) $data;
    }
}
