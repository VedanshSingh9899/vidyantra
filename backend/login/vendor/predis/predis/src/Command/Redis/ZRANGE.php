<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class ZRANGE extends RedisCommand
{
    


    public function getId()
    {
        return 'ZRANGE';
    }

    


    public function setArguments(array $arguments)
    {
        if (count($arguments) === 4) {
            $lastType = gettype($arguments[3]);

            if ($lastType === 'string' && strtoupper($arguments[3]) === 'WITHSCORES') {
                
                $arguments[3] = ['WITHSCORES' => true];
                $lastType = 'array';
            }

            if ($lastType === 'array') {
                $options = $this->prepareOptions(array_pop($arguments));
                $arguments = array_merge($arguments, $options);
            }
        }

        parent::setArguments($arguments);
    }

    






    protected function prepareOptions($options)
    {
        $opts = array_change_key_case($options, CASE_UPPER);
        $finalizedOpts = [];

        if (!empty($opts['WITHSCORES'])) {
            $finalizedOpts[] = 'WITHSCORES';
        }

        return $finalizedOpts;
    }

    




    protected function withScores()
    {
        $arguments = $this->getArguments();

        if (count($arguments) < 4) {
            return false;
        }

        return strtoupper($arguments[3]) === 'WITHSCORES';
    }

    


    public function parseResponse($data)
    {
        if ($this->withScores()) {
            $result = [];

            for ($i = 0; $i < count($data); ++$i) {
                if (is_array($data[$i])) {
                    $result[$data[$i][0]] = $data[$i][1]; 
                } else {
                    $result[$data[$i]] = $data[++$i];
                }
            }

            return $result;
        }

        return $data;
    }

    



    public function parseResp3Response($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $parsedData = [];

        foreach ($data as $element) {
            $parsedData[] = $this->parseResponse($element);
        }

        return $parsedData;
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
