<?php











namespace Predis\Command\Redis;

class HPEXPIREAT extends HEXPIRE
{
    public function getId()
    {
        return 'HPEXPIREAT';
    }
}
