<?php











namespace Predis\Command\Redis;

class HEXPIREAT extends HEXPIRE
{
    public function getId()
    {
        return 'HEXPIREAT';
    }
}
