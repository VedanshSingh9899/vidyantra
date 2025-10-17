<?php











namespace Predis\Command\Redis\TimeSeries;






class TSMREVRANGE extends TSMRANGE
{
    public function getId()
    {
        return 'TS.MREVRANGE';
    }
}
