<?php











namespace Predis\Command\Traits\By;

use Predis\Command\Command;
use UnexpectedValueException;




trait ByLexByScore
{
    private static $argumentsEnum = [
        'bylex' => 'BYLEX',
        'byscore' => 'BYSCORE',
    ];

    public function setArguments(array $arguments)
    {
        if (count($arguments) <= static::$byLexByScoreArgumentPositionOffset || false === $arguments[static::$byLexByScoreArgumentPositionOffset]) {
            parent::setArguments($arguments);

            return;
        }

        $argument = $arguments[static::$byLexByScoreArgumentPositionOffset];

        if (is_string($argument) && in_array(strtoupper($argument), self::$argumentsEnum)) {
            $argument = self::$argumentsEnum[$argument];
        } else {
            throw new UnexpectedValueException('By argument accepts only "bylex" and "byscore" values');
        }

        $argumentsBefore = array_slice($arguments, 0, static::$byLexByScoreArgumentPositionOffset);
        $argumentsAfter = array_slice($arguments, static::$byLexByScoreArgumentPositionOffset + 1);

        parent::setArguments(array_merge($argumentsBefore, [$argument], $argumentsAfter));
    }
}
