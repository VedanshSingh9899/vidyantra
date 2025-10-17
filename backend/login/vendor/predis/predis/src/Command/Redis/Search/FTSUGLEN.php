<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\Command as RedisCommand;






class FTSUGLEN extends RedisCommand
{
    public function getId()
    {
        return 'FT.SUGLEN';
    }
}
