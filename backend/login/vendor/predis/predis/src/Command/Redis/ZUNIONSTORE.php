<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;
use Predis\Command\Traits\Aggregate;
use Predis\Command\Traits\Keys;
use Predis\Command\Traits\Weights;




class ZUNIONSTORE extends RedisCommand
{
    use Keys {
        Keys::setArguments as setKeys;
    }
    use Weights {
        Weights::setArguments as setWeights;
    }
    use Aggregate{
        Aggregate::setArguments as setAggregate;
    }

    protected static $keysArgumentPositionOffset = 1;
    protected static $weightsArgumentPositionOffset = 2;
    protected static $aggregateArgumentPositionOffset = 3;

    


    public function getId()
    {
        return 'ZUNIONSTORE';
    }

    


    public function setArguments(array $arguments)
    {
        
        if (!isset($arguments[3]) && (isset($arguments[2]['weights']) || isset($arguments[2]['aggregate']))) {
            $options = array_pop($arguments);
            array_push($arguments, $options['weights'] ?? []);
            array_push($arguments, $options['aggregate'] ?? 'sum');
        }

        $this->setAggregate($arguments);
        $arguments = $this->getArguments();

        $this->setWeights($arguments);
        $arguments = $this->getArguments();

        $this->setKeys($arguments);
    }

    public function prefixKeys($prefix)
    {
        if ($arguments = $this->getArguments()) {
            $arguments[0] = "$prefix{$arguments[0]}";
            $length = ((int) $arguments[1]) + 2;

            for ($i = 2; $i < $length; ++$i) {
                $arguments[$i] = "$prefix{$arguments[$i]}";
            }

            $this->setRawArguments($arguments);
        }
    }
}
