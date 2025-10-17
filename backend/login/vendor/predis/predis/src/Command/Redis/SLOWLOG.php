<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class SLOWLOG extends RedisCommand
{
    


    public function getId()
    {
        return 'SLOWLOG';
    }

    


    public function parseResponse($data)
    {
        if (is_array($data)) {
            $log = [];

            foreach ($data as $index => $entry) {
                $log[$index] = [
                    'id' => $entry[0],
                    'timestamp' => $entry[1],
                    'duration' => $entry[2],
                    'command' => $entry[3],
                ];
            }

            return $log;
        }

        return $data;
    }

    



    public function parseResp3Response($data)
    {
        return $this->parseResponse($data);
    }
}
