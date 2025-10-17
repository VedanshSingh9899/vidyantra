<?php











namespace Predis\Command\Redis\TimeSeries;

use Predis\Command\PrefixableCommand as RedisCommand;






class TSMADD extends RedisCommand
{
    public function getId()
    {
        return 'TS.MADD';
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
