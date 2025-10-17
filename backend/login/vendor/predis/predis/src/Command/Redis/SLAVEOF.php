<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class SLAVEOF extends RedisCommand
{
    


    public function getId()
    {
        return 'SLAVEOF';
    }

    


    public function setArguments(array $arguments)
    {
        if (count($arguments) === 0 || $arguments[0] === 'NO ONE') {
            $arguments = ['NO', 'ONE'];
        }

        parent::setArguments($arguments);
    }
}
