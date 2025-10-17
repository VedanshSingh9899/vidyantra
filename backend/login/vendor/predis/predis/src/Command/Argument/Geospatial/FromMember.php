<?php











namespace Predis\Command\Argument\Geospatial;

class FromMember implements FromInterface
{
    private const KEYWORD = 'FROMMEMBER';

    


    private $member;

    public function __construct(string $member)
    {
        $this->member = $member;
    }

    


    public function toArray(): array
    {
        return [self::KEYWORD, $this->member];
    }
}
