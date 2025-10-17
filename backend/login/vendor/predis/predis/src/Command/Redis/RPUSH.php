<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class RPUSH extends RedisCommand
{
    


    public function getId()
    {
        return 'RPUSH';
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
