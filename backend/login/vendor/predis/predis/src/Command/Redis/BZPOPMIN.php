<?php











namespace Predis\Command\Redis;

use Predis\Command\Redis\AbstractCommand\BZPOPBase;











class BZPOPMIN extends BZPOPBase
{
    public function getId(): string
    {
        return 'BZPOPMIN';
    }
}
