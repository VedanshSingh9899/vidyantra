<?php











namespace Predis\Command\Redis\Json;

use Predis\Command\PrefixableCommand as RedisCommand;






class JSONDEL extends RedisCommand
{
    public function getId()
    {
        return 'JSON.DEL';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
