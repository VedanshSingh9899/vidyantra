<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class ZADD extends RedisCommand
{
    


    public function getId()
    {
        return 'ZADD';
    }

    


    public function setArguments(array $arguments)
    {
        if (is_array(end($arguments))) {
            foreach (array_pop($arguments) as $member => $score) {
                $arguments[] = $score;
                $arguments[] = $member;
            }
        }

        parent::setArguments($arguments);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
