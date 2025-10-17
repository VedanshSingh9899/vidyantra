<?php











namespace Predis\Command\Container;

use Predis\Response\Status;








class XGROUP extends AbstractContainer
{
    public function getContainerCommandId(): string
    {
        return 'xgroup';
    }
}
