<?php











namespace Predis\Command\Redis\Json;

use Predis\Command\PrefixableCommand as RedisCommand;






class JSONARRLEN extends RedisCommand
{
    public function getId()
    {
        return 'JSON.ARRLEN';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
