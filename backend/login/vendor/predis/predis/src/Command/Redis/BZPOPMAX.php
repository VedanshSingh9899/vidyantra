<?php











namespace Predis\Command\Redis;

use Predis\Command\Redis\AbstractCommand\BZPOPBase;











class BZPOPMAX extends BZPOPBase
{
    public function getId(): string
    {
        return 'BZPOPMAX';
    }
}
