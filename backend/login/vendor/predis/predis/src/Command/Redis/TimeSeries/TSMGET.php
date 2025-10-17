<?php











namespace Predis\Command\Redis\TimeSeries;

use Predis\Command\Command as RedisCommand;

class TSMGET extends RedisCommand
{
    public function getId()
    {
        return 'TS.MGET';
    }

    public function setArguments(array $arguments)
    {
        $processedArguments = [];
        $argumentsObject = array_shift($arguments);
        $commandArguments = $argumentsObject->toArray();

        array_push($processedArguments, 'FILTER', ...$arguments);

        parent::setArguments(array_merge(
            $commandArguments,
            $processedArguments
        ));
    }
}
