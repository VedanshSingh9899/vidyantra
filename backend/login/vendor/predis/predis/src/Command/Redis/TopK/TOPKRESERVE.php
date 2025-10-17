<?php











namespace Predis\Command\Redis\TopK;

use Predis\Command\PrefixableCommand as RedisCommand;






class TOPKRESERVE extends RedisCommand
{
    public function getId()
    {
        return 'TOPK.RESERVE';
    }

    public function setArguments(array $arguments)
    {
        switch (count($arguments)) {
            case 3:
                $arguments[] = 7; 
                $arguments[] = 0.9; 
                break;
            case 4:
                $arguments[] = 0.9; 
                break;
            default:
                parent::setArguments($arguments);

                return;
        }

        parent::setArguments($arguments);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
