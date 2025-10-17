<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class TYPE extends RedisCommand
{
    


    public function getId()
    {
        return 'TYPE';
    }

    


    public function parseResponse($data)
    {
        if (is_string($data)) {
            return $data;
        }

        
        switch ($data) {
            case 0: return 'none';
            case 1: return 'string';
            case 2: return 'set';
            case 3: return 'list';
            case 4: return 'zset';
            case 5: return 'hash';
            case 6: return 'stream';
            default: return $data;
        }
    }

    


    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
