<?php











namespace Predis\Command\Redis;

use InvalidArgumentException;
use Predis\Command\PrefixableCommand as RedisCommand;




class BITOP extends RedisCommand
{
    private const VALID_OPERATIONS = ['AND', 'OR', 'XOR', 'NOT', 'DIFF', 'DIFF1', 'ANDOR', 'ONE'];

    


    public function getId()
    {
        return 'BITOP';
    }

    


    public function setArguments(array $arguments)
    {
        if (count($arguments) === 3 && is_array($arguments[2])) {
            [$operation, $destination] = $arguments;
            $arguments = $arguments[2];
            array_unshift($arguments, $operation, $destination);
        }

        if (!empty($arguments)) {
            $operation = strtoupper($arguments[0]);
            if (!in_array($operation, self::VALID_OPERATIONS, false)) {
                throw new InvalidArgumentException('BITOP operation must be one of: AND, OR, XOR, NOT, DIFF, DIFF1, ANDOR, ONE');
            }
        }

        parent::setArguments($arguments);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixSkippingFirstArgument($prefix);
    }
}
