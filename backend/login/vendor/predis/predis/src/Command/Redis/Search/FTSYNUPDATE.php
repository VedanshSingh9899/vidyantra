<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\PrefixableCommand as RedisCommand;






class FTSYNUPDATE extends RedisCommand
{
    public function getId()
    {
        return 'FT.SYNUPDATE';
    }

    public function setArguments(array $arguments)
    {
        [$index, $synonymGroupId] = $arguments;
        $commandArguments = [];

        if (!empty($arguments[2])) {
            $commandArguments = $arguments[2]->toArray();
        }

        $terms = array_slice($arguments, 3);

        parent::setArguments(array_merge(
            [$index, $synonymGroupId],
            $commandArguments,
            $terms
        ));
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
