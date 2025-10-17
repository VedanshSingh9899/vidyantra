<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\Command as RedisCommand;






class FTSUGADD extends RedisCommand
{
    public function getId()
    {
        return 'FT.SUGADD';
    }

    public function setArguments(array $arguments)
    {
        [$key, $string, $score] = $arguments;
        $commandArguments = (!empty($arguments[3])) ? $arguments[3]->toArray() : [];

        parent::setArguments(array_merge(
            [$key, $string, $score],
            $commandArguments
        ));
    }
}
