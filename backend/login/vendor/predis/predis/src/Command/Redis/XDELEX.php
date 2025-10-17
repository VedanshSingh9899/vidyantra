<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class XDELEX extends RedisCommand
{
    


    public function getId()
    {
        return 'XDELEX';
    }

    


    public function setArguments(array $arguments)
    {
        $processedArguments = [$arguments[0], strtoupper($arguments[1])];

        array_push($processedArguments, 'IDS', strval(count($arguments[2])), ...$arguments[2]);

        parent::setArguments($processedArguments);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
