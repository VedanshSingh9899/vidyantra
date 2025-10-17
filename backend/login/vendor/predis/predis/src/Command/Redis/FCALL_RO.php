<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;






class FCALL_RO extends RedisCommand
{
    public function getId()
    {
        return 'FCALL_RO';
    }

    public function setArguments(array $arguments)
    {
        $processedArguments = array_merge([$arguments[0], count($arguments[1])], $arguments[1]);

        if (count($arguments) > 2) {
            for ($i = 2, $iMax = count($arguments); $i < $iMax; $i++) {
                $processedArguments[] = $arguments[$i];
            }
        }

        parent::setArguments($processedArguments);
    }
}
