<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class BRPOP extends RedisCommand
{
    


    public function getId()
    {
        return 'BRPOP';
    }

    


    public function setArguments(array $arguments)
    {
        if (count($arguments) === 2 && is_array($arguments[0])) {
            [$arguments, $timeout] = $arguments;
            array_push($arguments, $timeout);
        }

        parent::setArguments($arguments);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixSkippingLastArgument($prefix);
    }
}
