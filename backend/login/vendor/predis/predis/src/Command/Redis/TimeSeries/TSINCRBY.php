<?php











namespace Predis\Command\Redis\TimeSeries;

use Predis\Command\PrefixableCommand as RedisCommand;








class TSINCRBY extends RedisCommand
{
    public function getId()
    {
        return 'TS.INCRBY';
    }

    public function setArguments(array $arguments)
    {
        [$key, $value] = $arguments;
        $commandArguments = (!empty($arguments[2])) ? $arguments[2]->toArray() : [];

        parent::setArguments(array_merge(
            [$key, $value],
            $commandArguments
        ));
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
