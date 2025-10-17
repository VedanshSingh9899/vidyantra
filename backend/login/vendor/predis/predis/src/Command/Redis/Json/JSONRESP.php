<?php











namespace Predis\Command\Redis\Json;

use Predis\Command\PrefixableCommand as RedisCommand;






class JSONRESP extends RedisCommand
{
    public function getId()
    {
        return 'JSON.RESP';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
