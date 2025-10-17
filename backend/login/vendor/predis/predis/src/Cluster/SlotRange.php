<?php











namespace Predis\Cluster;

use Countable;
use OutOfBoundsException;




class SlotRange implements Countable
{
    


    public const MAX_SLOTS = 0x3FFF;

    




    protected $start;

    




    protected $end;

    




    protected $connection;

    public function __construct(int $start, int $end, string $connection)
    {
        if (!static::isValidRange($start, $end)) {
            throw new OutOfBoundsException("Invalid slot range $start-$end for `$connection`");
        }
        $this->start = $start;
        $this->end = $end;
        $this->connection = $connection;
    }

    







    public static function isValidRange($first, $last)
    {
        return $first >= 0x0000 && $first <= self::MAX_SLOTS && $last >= 0x0000 && $last <= self::MAX_SLOTS && $first <= $last;
    }

    




    public function getStart()
    {
        return $this->start;
    }

    




    public function getEnd()
    {
        return $this->end;
    }

    




    public function getConnection()
    {
        return $this->connection;
    }

    






    public function hasSlot(int $slot)
    {
        return $this->start <= $slot && $this->end >= $slot;
    }

    




    public function toArray(): array
    {
        return array_fill($this->start, $this->end - $this->start + 1, $this->connection);
    }

    




    public function count(): int
    {
        return $this->end - $this->start + 1;
    }

    






    public function hasIntersectionWith(SlotRange $slotRange): bool
    {
        return $this->start <= $slotRange->getEnd() && $this->end >= $slotRange->getStart();
    }
}
