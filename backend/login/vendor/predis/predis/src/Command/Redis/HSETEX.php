<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;
use UnexpectedValueException;

class HSETEX extends RedisCommand
{
    public const TTL_NULL = '';
    public const TTL_EX = 'ex';
    public const TTL_PX = 'px';
    public const TTL_EXAT = 'exat';
    public const TTL_PXAT = 'pxat';
    public const TTL_KEEP_TTL = 'keepttl';

    public const SET_NULL = '';
    public const SET_FNX = 'fnx';
    public const SET_FXX = 'fxx';

    


    private static $ttlModifierEnum = [
        self::TTL_EX => 'EX',
        self::TTL_PX => 'PX',
        self::TTL_EXAT => 'EXAT',
        self::TTL_PXAT => 'PXAT',
        self::TTL_KEEP_TTL => 'KEEPTTL',
    ];

    


    private static $setModifierEnum = [
        self::SET_FNX => 'FNX',
        self::SET_FXX => 'FXX',
    ];

    public function getId()
    {
        return 'HSETEX';
    }

    public function setArguments(array $arguments)
    {
        $processedArguments = [$arguments[0]];
        $flatArray = [];

        
        array_walk($arguments[1], function ($value, $key) use (&$flatArray) {
            array_push($flatArray, $key, $value);
        });

        
        if (!array_key_exists(2, $arguments)) {
            array_push($processedArguments, 'FIELDS', count($flatArray) / 2);
            $processedArguments = array_merge($processedArguments, $flatArray);
            parent::setArguments($processedArguments);

            return;
        }

        if ($arguments[2] !== '') {
            if (!in_array(strtoupper($arguments[2]), self::$setModifierEnum)) {
                $enumValues = implode(', ', array_keys(self::$setModifierEnum));
                throw new UnexpectedValueException("Modifier argument accepts only: {$enumValues} values");
            }

            $processedArguments[] = self::$setModifierEnum[strtolower($arguments[2])];
        }

        
        if (!array_key_exists(3, $arguments) || $arguments[3] == '') {
            array_push($processedArguments, 'FIELDS', count($flatArray) / 2);
            $processedArguments = array_merge($processedArguments, $flatArray);
            parent::setArguments($processedArguments);

            return;
        }

        if (!in_array(strtoupper($arguments[3]), self::$ttlModifierEnum)) {
            $enumValues = implode(', ', array_keys(self::$ttlModifierEnum));
            throw new UnexpectedValueException("Modifier argument accepts only: {$enumValues} values");
        }

        
        if (strtoupper($arguments[3]) === self::$ttlModifierEnum[self::TTL_KEEP_TTL]) {
            $processedArguments[] = self::$ttlModifierEnum[self::TTL_KEEP_TTL];
            array_push($processedArguments, 'FIELDS', count($flatArray) / 2);
            $processedArguments = array_merge($processedArguments, $flatArray);
            parent::setArguments($processedArguments);

            return;
        }

        if (!array_key_exists(4, $arguments) || !is_int($arguments[4])) {
            throw new UnexpectedValueException('Modifier value is missing or incorrect type');
        }

        
        array_push($processedArguments, self::$ttlModifierEnum[strtolower($arguments[3])], $arguments[4], 'FIELDS', count($flatArray) / 2);
        $processedArguments = array_merge($processedArguments, $flatArray);

        parent::setArguments($processedArguments);
    }
}
