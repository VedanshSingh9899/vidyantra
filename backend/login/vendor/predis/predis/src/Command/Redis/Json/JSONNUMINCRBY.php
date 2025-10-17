<?php











namespace Predis\Command\Redis\Json;

use Predis\Command\PrefixableCommand as RedisCommand;






class JSONNUMINCRBY extends RedisCommand
{
    public function getId()
    {
        return 'JSON.NUMINCRBY';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
