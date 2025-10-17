<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;

class XREADGROUP extends RedisCommand
{
    public function getId()
    {
        return 'XREADGROUP';
    }

    public function setArguments(array $arguments)
    {
        $processedArguments = ['GROUP', $arguments[0], $arguments[1]];

        if (count($arguments) >= 3 && null !== $arguments[2]) {
            array_push($processedArguments, 'COUNT', $arguments[2]);
        }

        if (count($arguments) >= 4 && null !== $arguments[3]) {
            array_push($processedArguments, 'BLOCK', $arguments[3]);
        }

        if (count($arguments) >= 5 && false !== $arguments[4]) {
            $processedArguments[] = 'NOACK';
        }

        $processedArguments[] = 'STREAMS';
        $keyOrIds = array_slice($arguments, 5);

        parent::setArguments(array_merge($processedArguments, $keyOrIds));
    }
}
