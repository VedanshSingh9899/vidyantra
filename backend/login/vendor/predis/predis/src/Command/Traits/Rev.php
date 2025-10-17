<?php











namespace Predis\Command\Traits;

use Predis\Command\Command;
use UnexpectedValueException;




trait Rev
{
    public function setArguments(array $arguments)
    {
        if (count($arguments) <= static::$revArgumentPositionOffset || false === $arguments[static::$revArgumentPositionOffset]) {
            parent::setArguments($arguments);

            return;
        }

        $argument = $arguments[static::$revArgumentPositionOffset];

        if (true === $argument) {
            $argument = 'REV';
        } else {
            throw new UnexpectedValueException('Wrong rev argument type');
        }

        $argumentsBefore = array_slice($arguments, 0, static::$revArgumentPositionOffset);
        $argumentsAfter = array_slice($arguments, static::$revArgumentPositionOffset + 1);

        parent::setArguments(array_merge($argumentsBefore, [$argument], $argumentsAfter));
    }
}
