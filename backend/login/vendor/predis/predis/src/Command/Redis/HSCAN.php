<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class HSCAN extends RedisCommand
{
    


    private $arguments;

    


    public function getId()
    {
        return 'HSCAN';
    }

    


    public function setArguments(array $arguments)
    {
        if (count($arguments) === 3 && is_array($arguments[2])) {
            $options = $this->prepareOptions(array_pop($arguments));
            $arguments = array_merge($arguments, $options);
        }

        $this->arguments = $arguments;
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

        if (!empty($options['NOVALUES']) && true === $options['NOVALUES']) {
            $normalized[] = 'NOVALUES';
        }

        return $normalized;
    }

    


    public function parseResponse($data)
    {
        if (!in_array('NOVALUES', $this->arguments, true)) {
            if (is_array($data)) {
                $fields = $data[1];
                $result = [];

                for ($i = 0; $i < count($fields); ++$i) {
                    $result[$fields[$i]] = $fields[++$i];
                }

                $data[1] = $result;
            }
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
