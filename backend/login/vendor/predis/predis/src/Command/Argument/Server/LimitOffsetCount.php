<?php











namespace Predis\Command\Argument\Server;

class LimitOffsetCount implements LimitInterface
{
    private const KEYWORD = 'LIMIT';

    


    private $offset;

    


    private $count;

    public function __construct(int $offset, int $count)
    {
        $this->offset = $offset;
        $this->count = $count;
    }

    


    public function toArray(): array
    {
        return [self::KEYWORD, $this->offset, $this->count];
    }
}
