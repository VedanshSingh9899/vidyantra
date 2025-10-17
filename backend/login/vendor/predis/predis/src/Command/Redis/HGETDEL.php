<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;

class HGETDEL extends RedisCommand
{
    public function getId()
    {
        return 'HGETDEL';
    }

    



    public function setArguments(array $arguments)
    {
        $processedArguments = [$arguments[0], 'FIELDS', count($arguments[1])];
        $processedArguments = array_merge($processedArguments, $arguments[1]);

        parent::setArguments($processedArguments);
    }
}
