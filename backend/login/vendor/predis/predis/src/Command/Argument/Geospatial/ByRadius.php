<?php











namespace Predis\Command\Argument\Geospatial;

class ByRadius extends AbstractBy
{
    private const KEYWORD = 'BYRADIUS';

    


    private $radius;

    public function __construct(int $radius, string $unit)
    {
        $this->radius = $radius;
        $this->setUnit($unit);
    }

    


    public function toArray(): array
    {
        return [self::KEYWORD, $this->radius, $this->unit];
    }
}
