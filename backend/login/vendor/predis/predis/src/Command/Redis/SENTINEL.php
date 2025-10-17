<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class SENTINEL extends RedisCommand
{
    


    public function getId()
    {
        return 'SENTINEL';
    }

    


    public function parseResponse($data)
    {
        $argument = $this->getArgument(0);
        $argument = is_null($argument) ? null : strtolower($argument);

        switch ($argument) {
            case 'masters':
            case 'slaves':
                return self::processMastersOrSlaves($data);

            default:
                return $data;
        }
    }

    






    protected static function processMastersOrSlaves(array $servers)
    {
        foreach ($servers as $idx => $node) {
            $processed = [];
            $count = count($node);

            for ($i = 0; $i < $count; ++$i) {
                $processed[$node[$i]] = $node[++$i];
            }

            $servers[$idx] = $processed;
        }

        return $servers;
    }
}
