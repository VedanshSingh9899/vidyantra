<?php











namespace Predis\Command\Redis;




class ZREVRANGEBYSCORE extends ZRANGEBYSCORE
{
    


    public function getId()
    {
        return 'ZREVRANGEBYSCORE';
    }
}
