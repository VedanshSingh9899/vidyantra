<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class HMGET extends RedisCommand
{
    


    public function getId()
    {
        return 'HMGET';
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
