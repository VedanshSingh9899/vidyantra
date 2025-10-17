<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;
use Predis\Command\Traits\With\WithScores;











class ZRANDMEMBER extends RedisCommand
{
    use WithScores;

    public function getId()
    {
        return 'ZRANDMEMBER';
    }
}
