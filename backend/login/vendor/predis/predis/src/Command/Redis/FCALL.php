<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;
use Predis\Command\Traits\Keys;






class FCALL extends RedisCommand
{
    use Keys;

    protected static $keysArgumentPositionOffset = 1;

    public function getId()
    {
        return 'FCALL';
    }

    public function prefixKeys($prefix)
    {
        $arguments = $this->getArguments();

        if (isset($arguments[1])) {
            $numkeys = $arguments[1];

            for ($i = 2; $i < $numkeys + 2; $i++) {
                $arguments[$i] = $prefix . $arguments[$i];
            }
        }

        $this->setRawArguments($arguments);
    }
}
