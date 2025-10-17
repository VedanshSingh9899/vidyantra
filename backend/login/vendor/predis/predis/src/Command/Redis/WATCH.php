<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class WATCH extends RedisCommand
{
    


    public function getId()
    {
        return 'WATCH';
    }

    


    public function setArguments(array $arguments)
    {
        if (isset($arguments[0]) && is_array($arguments[0])) {
            $arguments = $arguments[0];
        }

        parent::setArguments($arguments);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForAllArguments($prefix);
    }
}
