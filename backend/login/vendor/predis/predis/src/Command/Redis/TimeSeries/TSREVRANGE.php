<?php











namespace Predis\Command\Redis\TimeSeries;






class TSREVRANGE extends TSRANGE
{
    public function getId()
    {
        return 'TS.REVRANGE';
    }
}
