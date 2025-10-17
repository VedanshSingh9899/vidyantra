<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class TOUCH extends RedisCommand
{
    


    public function getId()
    {
        return 'TOUCH';
    }

    


    public function setArguments(array $arguments)
    {
        $arguments = self::normalizeArguments($arguments);

        parent::setArguments($arguments);
    }
}
