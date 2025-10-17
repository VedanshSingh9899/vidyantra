<?php











namespace Predis\Command\Argument\Geospatial;

class ByBox extends AbstractBy
{
    private const KEYWORD = 'BYBOX';

    


    private $width;

    


    private $height;

    public function __construct(int $width, int $height, string $unit)
    {
        $this->width = $width;
        $this->height = $height;
        $this->setUnit($unit);
    }

    


    public function toArray(): array
    {
        return [self::KEYWORD, $this->width, $this->height, $this->unit];
    }
}
