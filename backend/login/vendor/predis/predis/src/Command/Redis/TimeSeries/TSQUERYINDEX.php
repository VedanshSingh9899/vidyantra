<?php











namespace Predis\Command\Redis\TimeSeries;

use Predis\Command\Command as RedisCommand;






class TSQUERYINDEX extends RedisCommand
{
    public function getId()
    {
        return 'TS.QUERYINDEX';
    }
}
