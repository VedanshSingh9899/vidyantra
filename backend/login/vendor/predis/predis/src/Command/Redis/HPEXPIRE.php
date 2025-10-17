<?php











namespace Predis\Command\Redis;

class HPEXPIRE extends HEXPIRE
{
    public function getId()
    {
        return 'HPEXPIRE';
    }
}
