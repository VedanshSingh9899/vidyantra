<?php











namespace Predis\Command\Redis\Json;

use Predis\Command\PrefixableCommand as RedisCommand;
use Predis\Command\Traits\Json\NxXxArgument;






class JSONSET extends RedisCommand
{
    use NxXxArgument {
        setArguments as setSubcommand;
    }

    protected static $nxXxArgumentPositionOffset = 3;

    public function getId()
    {
        return 'JSON.SET';
    }

    public function setArguments(array $arguments)
    {
        $this->setSubcommand($arguments);
        $this->filterArguments();
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
