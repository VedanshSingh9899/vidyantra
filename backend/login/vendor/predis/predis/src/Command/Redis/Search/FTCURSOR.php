<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\PrefixableCommand as RedisCommand;

class FTCURSOR extends RedisCommand
{
    public function getId()
    {
        return 'FT.CURSOR';
    }

    public function setArguments(array $arguments)
    {
        [$subcommand, $index, $cursorId] = $arguments;
        $commandArguments = (!empty($arguments[3])) ? $arguments[3]->toArray() : [];

        parent::setArguments(array_merge(
            [$subcommand, $index, $cursorId],
            $commandArguments
        ));
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
