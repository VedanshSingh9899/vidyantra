<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;







class XGROUP extends RedisCommand
{
    public function getId()
    {
        return 'XGROUP';
    }

    public function setArguments(array $arguments)
    {
        switch ($arguments[0]) {
            case 'CREATE':
                $this->setCreateArguments($arguments);

                return;

            case 'SETID':
                $this->setSetIdArguments($arguments);

                return;

            default:
                parent::setArguments($arguments);
        }
    }

    



    private function setCreateArguments(array $arguments): void
    {
        $processedArguments = [$arguments[0], $arguments[1], $arguments[2], $arguments[3]];

        if (array_key_exists(4, $arguments) && true === $arguments[4]) {
            $processedArguments[] = 'MKSTREAM';
        }

        if (array_key_exists(5, $arguments)) {
            array_push($processedArguments, 'ENTRIESREAD', $arguments[5]);
        }

        parent::setArguments($processedArguments);
    }

    



    private function setSetIdArguments(array $arguments): void
    {
        $processedArguments = [$arguments[0], $arguments[1], $arguments[2], $arguments[3]];

        if (array_key_exists(4, $arguments)) {
            array_push($processedArguments, 'ENTRIESREAD', $arguments[4]);
        }

        parent::setArguments($processedArguments);
    }
}
