<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class SHUTDOWN extends RedisCommand
{
    


    public function getId()
    {
        return 'SHUTDOWN';
    }

    


    public function setArguments(array $arguments)
    {
        if (empty($arguments)) {
            parent::setArguments($arguments);

            return;
        }

        $processedArguments = [];

        if (array_key_exists(0, $arguments) && null !== $arguments[0]) {
            $processedArguments[] = ($arguments[0]) ? 'SAVE' : 'NOSAVE';
        }

        if (array_key_exists(1, $arguments) && false !== $arguments[1]) {
            $processedArguments[] = 'NOW';
        }

        if (array_key_exists(2, $arguments) && false !== $arguments[2]) {
            $processedArguments[] = 'FORCE';
        }

        if (array_key_exists(3, $arguments) && false !== $arguments[3]) {
            $processedArguments[] = 'ABORT';
        }

        parent::setArguments($processedArguments);
    }
}
