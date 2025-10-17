<?php











namespace Predis\Command\Redis;




class MSETNX extends MSET
{
    


    public function getId()
    {
        return 'MSETNX';
    }
}
