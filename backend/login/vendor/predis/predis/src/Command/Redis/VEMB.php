<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;

class VEMB extends RedisCommand
{
    


    private $isRaw = false;

    


    public function getId()
    {
        return 'VEMB';
    }

    



    public function setArguments(array $arguments)
    {
        $processedArguments = [$arguments[0], $arguments[1]];

        if (isset($arguments[2])) {
            $this->isRaw = true;
            $processedArguments[] = 'RAW';
        }

        parent::setArguments($processedArguments);
    }

    



    public function parseResponse($data)
    {
        if (!$this->isRaw) {
            return array_map(function ($value) { return (float) $value; }, $data);
        }

        $parsedData = [];

        for ($i = 0; $i < count($data); $i++) {
            if ($i > 1) {
                $parsedData[] = (float) $data[$i];
            } else {
                $parsedData[] = $data[$i];
            }
        }

        return $parsedData;
    }
}
