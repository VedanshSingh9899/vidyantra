<?php











namespace Predis\Command\Container;

use Predis\Response\Status;







class CLUSTER extends AbstractContainer
{
    public function getContainerCommandId(): string
    {
        return 'CLUSTER';
    }
}
