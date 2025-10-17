<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class XRANGE extends RedisCommand
{
    


    public function getId()
    {
        return 'XRANGE';
    }

    


    public function setArguments(array $arguments)
    {
        if (count($arguments) === 4) {
            $arguments[] = $arguments[3];
            $arguments[3] = 'COUNT';
        }

        parent::setArguments($arguments);
    }

    


    public function parseResponse($data)
    {
        $result = [];
        foreach ($data as $entry) {
            $processed = [];
            $count = count($entry[1]);

            for ($i = 0; $i < $count; ++$i) {
                $processed[$entry[1][$i]] = $entry[1][++$i];
            }

            $result[$entry[0]] = $processed;
        }

        return $result;
    }

    public function parseResp3Response($data)
    {
        return $this->parseResponse($data);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
