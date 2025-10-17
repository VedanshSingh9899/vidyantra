<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class ZSCAN extends RedisCommand
{
    


    public function getId()
    {
        return 'ZSCAN';
    }

    


    public function setArguments(array $arguments)
    {
        if (count($arguments) === 3 && is_array($arguments[2])) {
            $options = $this->prepareOptions(array_pop($arguments));
            $arguments = array_merge($arguments, $options);
        }

        parent::setArguments($arguments);
    }

    






    protected function prepareOptions($options)
    {
        $options = array_change_key_case($options, CASE_UPPER);
        $normalized = [];

        if (!empty($options['MATCH'])) {
            $normalized[] = 'MATCH';
            $normalized[] = $options['MATCH'];
        }

        if (!empty($options['COUNT'])) {
            $normalized[] = 'COUNT';
            $normalized[] = $options['COUNT'];
        }

        return $normalized;
    }

    


    public function parseResponse($data)
    {
        if (is_array($data)) {
            $members = $data[1];
            $result = [];

            for ($i = 0; $i < count($members); ++$i) {
                $result[$members[$i]] = (float) $members[++$i];
            }

            $data[1] = $result;
        }

        return $data;
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
