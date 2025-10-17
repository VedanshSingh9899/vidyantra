<?php











namespace Predis\Command\Traits\Get;

use UnexpectedValueException;

trait Get
{
    private static $getModifier = 'GET';

    public function setArguments(array $arguments)
    {
        $argumentsLength = count($arguments);

        if (static::$getArgumentPositionOffset >= $argumentsLength) {
            parent::setArguments($arguments);

            return;
        }

        if (!is_array($arguments[static::$getArgumentPositionOffset])) {
            throw new UnexpectedValueException('Wrong get argument type');
        }

        $patterns = [];

        foreach ($arguments[static::$getArgumentPositionOffset] as $pattern) {
            $patterns[] = self::$getModifier;
            $patterns[] = $pattern;
        }

        $argumentsBeforeKeys = array_slice($arguments, 0, static::$getArgumentPositionOffset);
        $argumentsAfterKeys = array_slice($arguments, static::$getArgumentPositionOffset + 1);

        parent::setArguments(array_merge($argumentsBeforeKeys, $patterns, $argumentsAfterKeys));
    }
}
