<?php











namespace Predis\Command\Redis;






class BZMPOP extends ZMPOP
{
    protected static $keysArgumentPositionOffset = 1;
    protected static $countArgumentPositionOffset = 3;
    protected static $modifierArgumentPositionOffset = 2;

    public function getId()
    {
        return 'BZMPOP';
    }
}
