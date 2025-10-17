<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class GEOADD extends RedisCommand
{
    


    public function getId()
    {
        return 'GEOADD';
    }

    


    public function setArguments(array $arguments)
    {
        if (count($arguments) === 2 && is_array($arguments[1])) {
            foreach (array_pop($arguments) as $item) {
                $arguments = array_merge($arguments, $item);
            }
        }

        parent::setArguments($arguments);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
