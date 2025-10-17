<?php











namespace Predis\Command\Redis;




class ZRANGEBYLEX extends ZRANGE
{
    


    public function getId()
    {
        return 'ZRANGEBYLEX';
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

        return $finalizedOpts;
    }

    


    protected function withScores()
    {
        return false;
    }
}
