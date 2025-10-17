<?php











namespace Predis\Command\Redis\TDigest;

use Predis\Command\PrefixableCommand as RedisCommand;






class TDIGESTMERGE extends RedisCommand
{
    public function getId()
    {
        return 'TDIGEST.MERGE';
    }

    public function setArguments(array $arguments)
    {
        $processedArguments = array_merge([$arguments[0], count($arguments[1])], $arguments[1]);

        for ($i = 2, $iMax = count($arguments); $i < $iMax; $i++) {
            if (is_int($arguments[$i]) && $arguments[$i] !== 0) {
                array_push($processedArguments, 'COMPRESSION', $arguments[$i]);
            } elseif (is_bool($arguments[$i]) && $arguments[$i]) {
                $processedArguments[] = 'OVERRIDE';
            }
        }

        parent::setArguments($processedArguments);
    }

    public function prefixKeys($prefix)
    {
        if ($arguments = $this->getArguments()) {
            $arguments[0] = $prefix . $arguments[0];

            for ($i = 2, $iMax = (int) $arguments[1] + 2; $i < $iMax; $i++) {
                $arguments[$i] = $prefix . $arguments[$i];
            }

            $this->setRawArguments($arguments);
        }
    }
}
