<?php











namespace Predis\Command\Redis;

class HPEXPIRETIME extends HEXPIRETIME
{
    public function getId()
    {
        return 'HPEXPIRETIME';
    }
}
