<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;

class XAUTOCLAIM extends RedisCommand
{
    public function getId()
    {
        return 'XAUTOCLAIM';
    }

    public function setArguments(array $arguments)
    {
        $processedArguments = array_splice($arguments, 0, 5);

        if (empty($arguments)) {
            parent::setArguments($processedArguments);

            return;
        }

        if ($arguments[0] !== null) {
            array_push($processedArguments, 'COUNT', $arguments[0]);
        }

        if (count($arguments) >= 2 && true === $arguments[1]) {
            $processedArguments[] = 'JUSTID';
        }

        parent::setArguments($processedArguments);
    }
}
