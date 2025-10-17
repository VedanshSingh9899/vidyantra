<?php











namespace Predis\Command\Redis\Json;

use Predis\Command\PrefixableCommand as RedisCommand;






class JSONARRINDEX extends RedisCommand
{
    public function getId()
    {
        return 'JSON.ARRINDEX';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
