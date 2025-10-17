<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class SUNIONSTORE extends RedisCommand
{
    


    public function getId()
    {
        return 'SUNIONSTORE';
    }

    


    public function setArguments(array $arguments)
    {
        if (count($arguments) === 2 && is_array($arguments[1])) {
            $arguments = array_merge([$arguments[0]], $arguments[1]);
        }

        parent::setArguments($arguments);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForAllArguments($prefix);
    }
}
