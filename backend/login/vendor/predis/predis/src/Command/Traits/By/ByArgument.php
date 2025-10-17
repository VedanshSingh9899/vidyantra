<?php











namespace Predis\Command\Traits\By;

use Predis\Command\Command;




trait ByArgument
{
    private $byModifier = 'BY';

    public function setArguments(array $arguments)
    {
        $argumentsLength = count($arguments);

        if (static::$byArgumentPositionOffset >= $argumentsLength || null === $arguments[static::$byArgumentPositionOffset]) {
            parent::setArguments($arguments);

            return;
        }

        $argument = $arguments[static::$byArgumentPositionOffset];
        $argumentsBefore = array_slice($arguments, 0, static::$byArgumentPositionOffset);
        $argumentsAfter = array_slice($arguments, static::$byArgumentPositionOffset + 1);

        parent::setArguments(array_merge($argumentsBefore, [$this->byModifier, $argument], $argumentsAfter));
    }
}
