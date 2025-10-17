<?php











namespace Predis\Command\Redis\TimeSeries;

use Predis\Command\PrefixableCommand as RedisCommand;






class TSCREATERULE extends RedisCommand
{
    public function getId()
    {
        return 'TS.CREATERULE';
    }

    public function setArguments(array $arguments)
    {
        [$sourceKey, $destKey, $aggregator, $bucketDuration] = $arguments;
        $processedArguments = [$sourceKey, $destKey, 'AGGREGATION', $aggregator, $bucketDuration];

        if (count($arguments) === 5) {
            $processedArguments[] = $arguments[4];
        }

        parent::setArguments($processedArguments);
    }

    public function prefixKeys($prefix)
    {
        if ($arguments = $this->getArguments()) {
            $arguments[0] = $prefix . $arguments[0];
            $arguments[1] = $prefix . $arguments[1];

            $this->setRawArguments($arguments);
        }
    }
}
