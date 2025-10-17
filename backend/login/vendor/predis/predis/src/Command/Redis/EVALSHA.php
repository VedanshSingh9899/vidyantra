<?php











namespace Predis\Command\Redis;




class EVALSHA extends EVAL_
{
    


    public function getId()
    {
        return 'EVALSHA';
    }

    




    public function getScriptHash()
    {
        return $this->getArgument(0);
    }
}
