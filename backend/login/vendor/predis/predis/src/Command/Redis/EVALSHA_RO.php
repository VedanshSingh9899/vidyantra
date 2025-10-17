<?php











namespace Predis\Command\Redis;







class EVALSHA_RO extends EVAL_RO
{
    public function getId()
    {
        return 'EVALSHA_RO';
    }
}
