<?php











namespace Predis\Command\Container;

use Predis\Response\Status;









class ACL extends AbstractContainer
{
    public function getContainerCommandId(): string
    {
        return 'acl';
    }
}
