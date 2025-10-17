<?php











namespace Predis\Command\Redis;




class ZRANGEBYSCORE extends ZRANGE
{
    


    public function getId()
    {
        return 'ZRANGEBYSCORE';
    }

    


    protected function prepareOptions($options)
    {
        $opts = array_change_key_case($options, CASE_UPPER);
        $finalizedOpts = [];

        if (isset($opts['LIMIT']) && is_array($opts['LIMIT'])) {
            $limit = array_change_key_case($opts['LIMIT'], CASE_UPPER);

            $finalizedOpts[] = 'LIMIT';
            $finalizedOpts[] = $limit['OFFSET'] ?? $limit[0];
            $finalizedOpts[] = $limit['COUNT'] ?? $limit[1];
        }

        return array_merge($finalizedOpts, parent::prepareOptions($options));
    }

    


    protected function withScores()
    {
        $arguments = $this->getArguments();

        for ($i = 3; $i < count($arguments); ++$i) {
            switch (strtoupper($arguments[$i])) {
                case 'WITHSCORES':
                    return true;

                case 'LIMIT':
                    $i += 2;
                    break;
            }
        }

        return false;
    }
}
