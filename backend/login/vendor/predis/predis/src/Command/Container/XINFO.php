<?php











namespace Predis\Command\Container;

use Predis\Command\Argument\Stream\XInfoStreamOptions;






class XINFO extends AbstractContainer
{
    public function getContainerCommandId(): string
    {
        return 'XINFO';
    }
}
