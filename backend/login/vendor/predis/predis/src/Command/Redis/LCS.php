<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;






class LCS extends RedisCommand
{
    public function getId()
    {
        return 'LCS';
    }

    public function setArguments(array $arguments)
    {
        if (isset($arguments[2]) && $arguments[2]) {
            $arguments[2] = 'LEN';
        }

        if (isset($arguments[3]) && $arguments[3]) {
            $arguments[3] = 'IDX';
        }

        if (isset($arguments[5]) && $arguments[5]) {
            $arguments[5] = 'WITHMATCHLEN';
        }

        if (isset($arguments[4])) {
            if ($arguments[4] !== 0) {
                $argumentsBefore = array_slice($arguments, 0, 4);
                $argumentsAfter = array_slice($arguments, 5);
                $arguments = array_merge($argumentsBefore, ['MINMATCHLEN', $arguments[4]], $argumentsAfter);
            } else {
                $arguments[4] = false;
            }
        }

        parent::setArguments($arguments);
        $this->filterArguments();
    }

    public function parseResponse($data)
    {
        if (is_array($data)) {
            if ($data !== array_values($data)) {
                return $data; 
            }

            return [$data[0] => $data[1], $data[2] => $data[3]];
        }

        return $data;
    }
}
