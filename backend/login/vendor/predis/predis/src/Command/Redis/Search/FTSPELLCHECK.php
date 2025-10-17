<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\PrefixableCommand as RedisCommand;

class FTSPELLCHECK extends RedisCommand
{
    public function getId()
    {
        return 'FT.SPELLCHECK';
    }

    public function setArguments(array $arguments)
    {
        
        if (in_array('DIALECT', $arguments)) {
            parent::setArguments($arguments);

            return;
        }

        [$index, $query] = $arguments;

        if (!empty($arguments[2]) && !in_array('DIALECT', $arguments[2]->toArray())) {
            
            $arguments[2]->dialect(2);
        }

        $commandArguments = ['DIALECT', 2];

        if (!empty($arguments[2])) {
            $commandArguments = $arguments[2]->toArray();
        }

        parent::setArguments(array_merge(
            [$index, $query],
            $commandArguments
        ));
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
