<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\Command as RedisCommand;
use Predis\Command\Redis\CONFIG;











class FTCONFIG extends RedisCommand
{
    public function getId()
    {
        return 'FT.CONFIG';
    }
}
