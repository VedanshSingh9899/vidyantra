<?php











namespace Predis\Command\Redis\CuckooFilter;







class CFINSERTNX extends CFINSERT
{
    public function getId()
    {
        return 'CF.INSERTNX';
    }
}
