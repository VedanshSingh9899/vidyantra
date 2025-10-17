<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class XACKDEL extends RedisCommand
{
    


    public function getId()
    {
        return 'XACKDEL';
    }

    


    public function setArguments(array $arguments)
    {
        $processedArguments = [$arguments[0], $arguments[1], strtoupper($arguments[2])];

        array_push($processedArguments, 'IDS', strval(count($arguments[3])), ...$arguments[3]);

        parent::setArguments($processedArguments);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
