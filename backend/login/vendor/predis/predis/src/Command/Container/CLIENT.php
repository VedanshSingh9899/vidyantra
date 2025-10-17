<?php











namespace Predis\Command\Container;

use Predis\Response\Status;










class CLIENT extends AbstractContainer
{
    public function getContainerCommandId(): string
    {
        return 'CLIENT';
    }
}
