<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\Command as RedisCommand;






class FTSUGGET extends RedisCommand
{
    public function getId()
    {
        return 'FT.SUGGET';
    }

    public function setArguments(array $arguments)
    {
        [$key, $prefix] = $arguments;
        $commandArguments = (!empty($arguments[2])) ? $arguments[2]->toArray() : [];

        parent::setArguments(array_merge(
            [$key, $prefix],
            $commandArguments
        ));
    }
}
