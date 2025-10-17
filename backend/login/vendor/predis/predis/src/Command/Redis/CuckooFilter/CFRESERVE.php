<?php











namespace Predis\Command\Redis\CuckooFilter;

use Predis\Command\PrefixableCommand as RedisCommand;
use Predis\Command\Traits\BloomFilters\BucketSize;
use Predis\Command\Traits\BloomFilters\Expansion;
use Predis\Command\Traits\BloomFilters\MaxIterations;

class CFRESERVE extends RedisCommand
{
    use BucketSize {
        BucketSize::setArguments as setBucketSize;
    }
    use MaxIterations {
        MaxIterations::setArguments as setMaxIterations;
    }
    use Expansion {
        Expansion::setArguments as setExpansion;
    }

    protected static $bucketSizeArgumentPositionOffset = 2;
    protected static $maxIterationsArgumentPositionOffset = 3;
    protected static $expansionArgumentPositionOffset = 4;

    public function getId()
    {
        return 'CF.RESERVE';
    }

    public function setArguments(array $arguments)
    {
        $this->setExpansion($arguments);
        $arguments = $this->getArguments();

        $this->setMaxIterations($arguments);
        $arguments = $this->getArguments();

        $this->setBucketSize($arguments);
        $this->filterArguments();
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
