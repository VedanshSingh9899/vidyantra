<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;
use Predis\Command\Traits\Keys;











class ZDIFFSTORE extends RedisCommand
{
    use Keys {
        Keys::setArguments as setKeys;
    }

    public static $keysArgumentPositionOffset = 1;

    public function getId()
    {
        return 'ZDIFFSTORE';
    }
}
