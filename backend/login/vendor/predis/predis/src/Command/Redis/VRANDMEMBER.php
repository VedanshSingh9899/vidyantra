<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;

class VRANDMEMBER extends RedisCommand
{
    


    public function getId(): string
    {
        return 'VRANDMEMBER';
    }

    



    public function setArguments(array $arguments)
    {
        $lastArg = array_pop($arguments);

        if (!is_null($lastArg)) {
            $arguments[] = $lastArg;
        }

        parent::setArguments($arguments);
    }
}
