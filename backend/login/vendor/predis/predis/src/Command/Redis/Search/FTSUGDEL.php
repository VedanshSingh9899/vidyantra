<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\Command as RedisCommand;






class FTSUGDEL extends RedisCommand
{
    public function getId()
    {
        return 'FT.SUGDEL';
    }
}
