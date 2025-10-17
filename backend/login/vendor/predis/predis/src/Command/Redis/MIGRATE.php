<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class MIGRATE extends RedisCommand
{
    


    public function getId()
    {
        return 'MIGRATE';
    }

    


    public function setArguments(array $arguments)
    {
        if (is_array(end($arguments))) {
            foreach (array_pop($arguments) as $modifier => $value) {
                $modifier = strtoupper($modifier);

                if ($modifier === 'COPY' && $value == true) {
                    $arguments[] = $modifier;
                }

                if ($modifier === 'REPLACE' && $value == true) {
                    $arguments[] = $modifier;
                }
            }
        }

        parent::setArguments($arguments);
    }

    public function prefixKeys($prefix)
    {
        if ($arguments = $this->getArguments()) {
            $arguments[2] = "$prefix{$arguments[2]}";
            $this->setRawArguments($arguments);
        }
    }
}
