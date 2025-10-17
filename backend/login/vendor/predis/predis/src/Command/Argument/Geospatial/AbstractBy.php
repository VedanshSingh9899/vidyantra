<?php











namespace Predis\Command\Argument\Geospatial;

use UnexpectedValueException;

abstract class AbstractBy implements ByInterface
{
    


    private static $unitEnum = ['m', 'km', 'ft', 'mi'];

    


    protected $unit;

    


    abstract public function toArray(): array;

    



    protected function setUnit(string $unit): void
    {
        if (!in_array($unit, self::$unitEnum, true)) {
            throw new UnexpectedValueException('Wrong value given for unit');
        }

        $this->unit = $unit;
    }
}
