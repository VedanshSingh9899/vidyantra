<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;
use Predis\Command\Traits\Keys;







class EVAL_RO extends RedisCommand
{
    use Keys;

    protected static $keysArgumentPositionOffset = 1;

    public function getId()
    {
        return 'EVAL_RO';
    }
}
