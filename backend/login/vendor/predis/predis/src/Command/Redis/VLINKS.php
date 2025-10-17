<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;
use Predis\Command\Redis\Utils\CommandUtility;

class VLINKS extends RedisCommand
{
    


    private $withScores = false;

    


    public function getId()
    {
        return 'VLINKS';
    }

    



    public function setArguments(array $arguments)
    {
        $lastArg = array_pop($arguments);

        if (is_bool($lastArg)) {
            $this->withScores = $lastArg;
            $arguments[] = 'WITHSCORES';
        } else {
            $arguments[] = $lastArg;
        }

        parent::setArguments($arguments);
    }

    



    public function parseResponse($data): ?array
    {
        if (!is_null($data)) {
            if ($this->withScores) {
                foreach ($data as $key => $value) {
                    $data[$key] = CommandUtility::arrayToDictionary($value, function ($key, $value) {
                        return [$key, (float) $value];
                    });
                }
            }
        }

        return $data;
    }
}
