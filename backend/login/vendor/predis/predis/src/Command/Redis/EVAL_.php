<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class EVAL_ extends RedisCommand
{
    


    public function getId()
    {
        return 'EVAL';
    }

    




    public function getScriptHash()
    {
        return sha1($this->getArgument(0));
    }

    public function prefixKeys($prefix)
    {
        if ($arguments = $this->getArguments()) {
            for ($i = 2; $i < $arguments[1] + 2; ++$i) {
                $arguments[$i] = "$prefix{$arguments[$i]}";
            }

            $this->setRawArguments($arguments);
        }
    }
}
