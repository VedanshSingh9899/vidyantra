<?php











namespace Predis\Command\Redis\CountMinSketch;

use Predis\Command\PrefixableCommand as RedisCommand;








class CMSMERGE extends RedisCommand
{
    public function getId()
    {
        return 'CMS.MERGE';
    }

    public function setArguments(array $arguments)
    {
        $processedArguments = array_merge([$arguments[0], count($arguments[1])], $arguments[1]);

        if (!empty($arguments[2])) {
            $processedArguments[] = 'WEIGHTS';
            $processedArguments = array_merge($processedArguments, $arguments[2]);
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
