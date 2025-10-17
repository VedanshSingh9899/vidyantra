<?php











namespace Predis\Command\Container\Json;

use Predis\Command\Container\AbstractContainer;





class JSONDEBUG extends AbstractContainer
{
    public function getContainerCommandId(): string
    {
        return 'JSONDEBUG';
    }
}
