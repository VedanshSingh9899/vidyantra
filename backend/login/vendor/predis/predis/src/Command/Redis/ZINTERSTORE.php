<?php











namespace Predis\Command\Redis;




class ZINTERSTORE extends ZUNIONSTORE
{
    


    public function getId()
    {
        return 'ZINTERSTORE';
    }
}
