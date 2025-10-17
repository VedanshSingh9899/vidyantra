<?php











namespace Predis\Command\Container\Search;

use Predis\Command\Argument\Search\CursorArguments;
use Predis\Command\Container\AbstractContainer;
use Predis\Response\Status;





class FTCURSOR extends AbstractContainer
{
    public function getContainerCommandId(): string
    {
        return 'FTCURSOR';
    }
}
