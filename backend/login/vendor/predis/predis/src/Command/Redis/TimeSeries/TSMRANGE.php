<?php











namespace Predis\Command\Redis\TimeSeries;

use Predis\Command\Command as RedisCommand;






class TSMRANGE extends RedisCommand
{
    public function getId()
    {
        return 'TS.MRANGE';
    }

    public function setArguments(array $arguments)
    {
        [$fromTimestamp, $toTimestamp] = $arguments;
        $commandArguments = $arguments[2]->toArray();

        parent::setArguments(array_merge(
            [$fromTimestamp, $toTimestamp],
            $commandArguments
        ));
    }
}
