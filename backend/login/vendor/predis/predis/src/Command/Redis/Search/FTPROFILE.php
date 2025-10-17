<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\PrefixableCommand as RedisCommand;






class FTPROFILE extends RedisCommand
{
    public function getId()
    {
        return 'FT.PROFILE';
    }

    public function setArguments(array $arguments)
    {
        [$index, $arguments] = $arguments;

        parent::setArguments(array_merge(
            [$index],
            $arguments->toArray()
        ));
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
