<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class SREM extends RedisCommand
{
    


    public function getId()
    {
        return 'SREM';
    }

    


    public function setArguments(array $arguments)
    {
        $arguments = self::normalizeVariadic($arguments);

        parent::setArguments($arguments);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
