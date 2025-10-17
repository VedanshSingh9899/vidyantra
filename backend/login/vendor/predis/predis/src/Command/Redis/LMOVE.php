<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;

class LMOVE extends RedisCommand
{
    public function getId()
    {
        return 'LMOVE';
    }

    public function prefixKeys($prefix)
    {
        if ($arguments = $this->getArguments()) {
            $arguments[0] = $prefix . $arguments[0];
            $arguments[1] = $prefix . $arguments[1];

            $this->setRawArguments($arguments);
        }
    }
}
