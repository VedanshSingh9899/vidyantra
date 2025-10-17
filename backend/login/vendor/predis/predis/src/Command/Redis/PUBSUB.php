<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class PUBSUB extends RedisCommand
{
    


    public function getId()
    {
        return 'PUBSUB';
    }

    


    public function parseResponse($data)
    {
        switch (strtolower($this->getArgument(0))) {
            case 'numsub':
                return self::processNumsub($data);

            default:
                return $data;
        }
    }

    






    protected static function processNumsub(array $channels)
    {
        $processed = [];
        $count = count($channels);

        for ($i = 0; $i < $count; ++$i) {
            $processed[$channels[$i]] = $channels[++$i];
        }

        return $processed;
    }
}
