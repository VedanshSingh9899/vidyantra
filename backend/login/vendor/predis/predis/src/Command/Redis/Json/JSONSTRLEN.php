<?php











namespace Predis\Command\Redis\Json;

use Predis\Command\PrefixableCommand as RedisCommand;






class JSONSTRLEN extends RedisCommand
{
    public function getId()
    {
        return 'JSON.STRLEN';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
