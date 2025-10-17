<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;
use Predis\Command\Redis\Utils\CommandUtility;

class VINFO extends RedisCommand
{
    


    public function getId(): string
    {
        return 'VINFO';
    }

    



    public function parseResponse($data): ?array
    {
        if (!is_null($data)) {
            return CommandUtility::arrayToDictionary($data);
        }

        return $data;
    }
}
