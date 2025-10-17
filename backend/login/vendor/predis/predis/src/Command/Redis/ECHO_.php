<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class ECHO_ extends RedisCommand
{
    


    public function getId()
    {
        return 'ECHO';
    }
}
