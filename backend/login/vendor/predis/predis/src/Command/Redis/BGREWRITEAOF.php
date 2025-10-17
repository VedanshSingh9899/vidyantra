<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class BGREWRITEAOF extends RedisCommand
{
    


    public function getId()
    {
        return 'BGREWRITEAOF';
    }

    


    public function parseResponse($data)
    {
        return $data == 'Background append only file rewriting started';
    }
}
