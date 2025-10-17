<?php











namespace Predis\Command\Redis\Json;

use Predis\Command\PrefixableCommand as RedisCommand;






class JSONMSET extends RedisCommand
{
    public function getId()
    {
        return 'JSON.MSET';
    }

    public function prefixKeys($prefix)
    {
        if ($arguments = $this->getArguments()) {
            for ($i = 0, $l = count($arguments); $i < $l; $i += 3) {
                $arguments[$i] = $prefix . $arguments[$i];
            }

            $this->setArguments($arguments);
        }
    }
}
