<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;

class VCARD extends RedisCommand
{
    


    public function getId()
    {
        return 'VCARD';
    }
}
