<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class XSETID extends RedisCommand
{
    


    public function getId()
    {
        return 'XSETID';
    }

    public function setArguments(array $arguments): void
    {
        $preparedArguments = array_slice($arguments, 0, 2);

        if (isset($arguments[2])) {
            array_push($preparedArguments, 'ENTRIESADDED', $arguments[2]);
        }

        if (isset($arguments[3])) {
            array_push($preparedArguments, 'MAXDELETEDID', $arguments[3]);
        }

        parent::setArguments($preparedArguments);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
