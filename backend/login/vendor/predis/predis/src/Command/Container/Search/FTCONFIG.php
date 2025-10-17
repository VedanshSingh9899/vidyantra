<?php











namespace Predis\Command\Container\Search;

use Predis\Command\Container\AbstractContainer;
use Predis\Response\Status;






class FTCONFIG extends AbstractContainer
{
    public function getContainerCommandId(): string
    {
        return 'FTCONFIG';
    }
}
