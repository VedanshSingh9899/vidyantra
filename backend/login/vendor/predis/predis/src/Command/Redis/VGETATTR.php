<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;

class VGETATTR extends RedisCommand
{
    


    private $asJson = false;

    


    public function getId()
    {
        return 'VGETATTR';
    }

    



    public function setArguments(array $arguments)
    {
        $lastArg = array_pop($arguments);

        if (is_bool($lastArg)) {
            $this->asJson = $lastArg;
        } else {
            $arguments[] = $lastArg;
        }

        parent::setArguments($arguments);
    }

    



    public function parseResponse($data)
    {
        if (!$this->asJson && !is_null($data)) {
            return json_decode($data, true);
        }

        return $data;
    }

    



    public function parseResp3Response($data)
    {
        return $this->parseResponse($data);
    }
}
