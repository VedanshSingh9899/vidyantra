<?php











namespace Predis\Command\Redis\TDigest;

use Predis\Command\PrefixableCommand as RedisCommand;






class TDIGESTCREATE extends RedisCommand
{
    public function getId()
    {
        return 'TDIGEST.CREATE';
    }

    public function setArguments(array $arguments)
    {
        if (!empty($arguments[1])) {
            $arguments[2] = $arguments[1];
            $arguments[1] = 'COMPRESSION';
        } elseif (array_key_exists(1, $arguments) && $arguments[1] < 1) {
            array_pop($arguments);
        }

        parent::setArguments($arguments);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
