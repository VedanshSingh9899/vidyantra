<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\PrefixableCommand as RedisCommand;

class FTDROPINDEX extends RedisCommand
{
    public function getId()
    {
        return 'FT.DROPINDEX';
    }

    public function setArguments(array $arguments)
    {
        [$index] = $arguments;
        $commandArguments = [];

        if (!empty($arguments[1])) {
            $commandArguments = $arguments[1]->toArray();
        }

        parent::setArguments(array_merge(
            [$index],
            $commandArguments
        ));
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
