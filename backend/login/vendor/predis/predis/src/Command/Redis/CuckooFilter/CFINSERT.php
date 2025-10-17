<?php











namespace Predis\Command\Redis\CuckooFilter;

use Predis\Command\PrefixableCommand as RedisCommand;
use Predis\Command\Traits\BloomFilters\Capacity;
use Predis\Command\Traits\BloomFilters\Items;
use Predis\Command\Traits\BloomFilters\NoCreate;

class CFINSERT extends RedisCommand
{
    use Capacity {
        Capacity::setArguments as setCapacity;
    }
    use NoCreate {
        NoCreate::setArguments as setNoCreate;
    }
    use Items {
        Items::setArguments as setItems;
    }

    protected static $capacityArgumentPositionOffset = 1;
    protected static $noCreateArgumentPositionOffset = 2;
    protected static $itemsArgumentPositionOffset = 3;

    public function getId()
    {
        return 'CF.INSERT';
    }

    public function setArguments(array $arguments)
    {
        $this->setNoCreate($arguments);
        $arguments = $this->getArguments();

        $this->setItems($arguments);
        $arguments = $this->getArguments();

        $this->setCapacity($arguments);
        $this->filterArguments();
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
