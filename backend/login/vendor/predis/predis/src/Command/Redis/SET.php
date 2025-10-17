<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class SET extends RedisCommand
{
    


    public function getId()
    {
        return 'SET';
    }

    public function setArguments(array $arguments)
    {
        foreach ($arguments as $index => $value) {
            if ($index < 2) {
                continue;
            }

            if (false === $value || null === $value) {
                unset($arguments[$index]);
            }
        }

        parent::setArguments($arguments);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
