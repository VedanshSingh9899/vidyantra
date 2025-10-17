<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class XTRIM extends RedisCommand
{
    


    public function getId()
    {
        return 'XTRIM';
    }

    


    public function setArguments(array $arguments)
    {
        $args = [];
        $options = $arguments[3] ?? [];

        $args[] = $arguments[0];
        
        if (is_array($arguments[1])) {
            array_push($args, ...$arguments[1]);
        } else {
            $args[] = $arguments[1];
        }

        $args[] = $arguments[2];
        if (isset($options['limit'])) {
            $args[] = 'LIMIT';
            $args[] = $options['limit'];
        }

        if (isset($options['trimming'])) {
            $args[] = strtoupper($options['trimming']);
        }

        parent::setArguments($args);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
