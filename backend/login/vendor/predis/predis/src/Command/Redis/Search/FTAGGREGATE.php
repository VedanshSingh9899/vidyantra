<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\PrefixableCommand as RedisCommand;







class FTAGGREGATE extends RedisCommand
{
    public function getId()
    {
        return 'FT.AGGREGATE';
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

        $commandArguments = (!empty($arguments[2])) ? $arguments[2]->toArray() : ['DIALECT', 2];

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
