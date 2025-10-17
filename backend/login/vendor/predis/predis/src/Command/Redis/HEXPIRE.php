<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;
use UnexpectedValueException;

class HEXPIRE extends RedisCommand
{
    


    protected $flagsEnum = [
        'NX', 'XX', 'GT', 'LT',
    ];

    public function getId()
    {
        return 'HEXPIRE';
    }

    public function setArguments(array $arguments)
    {
        $processedArguments = [$arguments[0], $arguments[1]];

        if (array_key_exists(3, $arguments) && null !== $arguments[3]) {
            if (in_array(strtoupper($arguments[3]), $this->flagsEnum, true)) {
                $processedArguments[] = strtoupper($arguments[3]);
            } else {
                throw new UnexpectedValueException('Unsupported flag value');
            }
        }

        if (array_key_exists(2, $arguments) && null !== $arguments[2]) {
            array_push($processedArguments, 'FIELDS', count($arguments[2]));
            $processedArguments = array_merge($processedArguments, $arguments[2]);
        }

        parent::setArguments($processedArguments);
    }
}
