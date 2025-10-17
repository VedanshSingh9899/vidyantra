<?php











namespace Predis\Command\Redis\Json;

use Predis\Command\PrefixableCommand as RedisCommand;
use Predis\Command\Traits\Json\Indent;
use Predis\Command\Traits\Json\Newline;
use Predis\Command\Traits\Json\Space;






class JSONGET extends RedisCommand
{
    use Indent {
        Indent::setArguments as setIndent;
    }
    use Newline {
        Newline::setArguments as setNewline;
    }
    use Space {
        Space::setArguments as setSpace;
    }

    protected static $indentArgumentPositionOffset = 1;
    protected static $newlineArgumentPositionOffset = 2;
    protected static $spaceArgumentPositionOffset = 3;

    public function getId()
    {
        return 'JSON.GET';
    }

    public function setArguments(array $arguments)
    {
        $this->setSpace($arguments);
        $arguments = $this->getArguments();

        $this->setNewline($arguments);
        $arguments = $this->getArguments();

        $this->setIndent($arguments);
        $this->filterArguments();
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
