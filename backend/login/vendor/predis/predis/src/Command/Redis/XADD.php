<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class XADD extends RedisCommand
{
    


    public function getId()
    {
        return 'XADD';
    }

    


    public function setArguments(array $arguments)
    {
        $args = [];

        $args[] = $arguments[0];
        $options = $arguments[3] ?? [];

        if (isset($options['nomkstream']) && $options['nomkstream']) {
            $args[] = 'NOMKSTREAM';
        }

        if (isset($options['trim']) && is_array($options['trim'])) {
            array_push($args, ...$options['trim']);

            if (isset($options['limit'])) {
                $args[] = 'LIMIT';
                $args[] = $options['limit'];
            }
        }

        if (isset($options['trimming'])) {
            $args[] = strtoupper($options['trimming']);
        }

        
        $args[] = $arguments[2] ?? '*';

        if (isset($arguments[1]) && is_array($arguments[1])) {
            foreach ($arguments[1] as $key => $val) {
                $args[] = $key;
                $args[] = $val;
            }
        }

        parent::setArguments($args);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
