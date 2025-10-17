<?php











namespace Predis\Cluster;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use OutOfBoundsException;
use Predis\Connection\NodeConnectionInterface;
use ReturnTypeWillChange;
use Traversable;




class SlotMap implements ArrayAccess, IteratorAggregate, Countable
{
    




    private $slotRanges = [];

    






    public static function isValid($slot)
    {
        return $slot >= 0 && $slot <= SlotRange::MAX_SLOTS;
    }

    







    public static function isValidRange($first, $last)
    {
        return SlotRange::isValidRange($first, $last);
    }

    


    public function reset()
    {
        $this->slotRanges = [];
    }

    




    public function isEmpty()
    {
        return empty($this->slotRanges);
    }

    






    public function toArray()
    {
        return array_reduce(
            $this->slotRanges,
            function ($carry, $slotRange) {
                return $carry + $slotRange->toArray();
            },
            []
        );
    }

    




    public function getNodes()
    {
        return array_unique(array_map(
            function ($slotRange) {
                return $slotRange->getConnection();
            },
            $this->slotRanges
        ));
    }

    




    public function getSlotRanges()
    {
        return $this->slotRanges;
    }

    








    public function setSlots($first, $last, $connection)
    {
        if (!static::isValidRange($first, $last)) {
            throw new OutOfBoundsException("Invalid slot range $first-$last for `$connection`");
        }

        $targetSlotRange = new SlotRange($first, $last, (string) $connection);

        
        $gaps = $this->getGaps($this->slotRanges);

        $results = $this->slotRanges;

        foreach ($gaps as $gap) {
            if (!$gap->hasIntersectionWith($targetSlotRange)) {
                continue;
            }

            
            $results[] = new SlotRange(
                max($gap->getStart(), $targetSlotRange->getStart()),
                min($gap->getEnd(), $targetSlotRange->getEnd()),
                $targetSlotRange->getConnection()
            );
        }

        $this->sortSlotRanges($results);

        $results = $this->compactSlotRanges($results);

        $this->slotRanges = $results;
    }

    







    public function getSlots($first, $last)
    {
        if (!static::isValidRange($first, $last)) {
            throw new OutOfBoundsException("Invalid slot range $first-$last");
        }

        $placeHolder = new NullSlotRange($first, $last);

        $intersections = [];
        foreach ($this->slotRanges as $slotRange) {
            if (!$placeHolder->hasIntersectionWith($slotRange)) {
                continue;
            }

            $intersections[] = new SlotRange(
                max($placeHolder->getStart(), $slotRange->getStart()),
                min($placeHolder->getEnd(), $slotRange->getEnd()),
                $slotRange->getConnection()
            );
        }

        return array_reduce(
            $intersections,
            function ($carry, $slotRange) {
                return $carry + $slotRange->toArray();
            },
            []
        );
    }

    






    #[ReturnTypeWillChange]
    public function offsetExists($slot)
    {
        return $this->findRangeBySlot($slot) !== false;
    }

    






    #[ReturnTypeWillChange]
    public function offsetGet($slot)
    {
        $found = $this->findRangeBySlot($slot);

        return $found ? $found->getConnection() : null;
    }

    







    #[ReturnTypeWillChange]
    public function offsetSet($slot, $connection)
    {
        if (!static::isValid($slot)) {
            throw new OutOfBoundsException("Invalid slot $slot for `$connection`");
        }

        $this->offsetUnset($slot);
        $this->setSlots($slot, $slot, $connection);
    }

    






    #[ReturnTypeWillChange]
    public function offsetUnset($slot)
    {
        if (!static::isValid($slot)) {
            throw new OutOfBoundsException("Invalid slot $slot");
        }

        $results = [];
        foreach ($this->slotRanges as $slotRange) {
            if (!$slotRange->hasSlot($slot)) {
                $results[] = $slotRange;
            }

            if (static::isValidRange($slotRange->getStart(), $slot - 1)) {
                $results[] = new SlotRange($slotRange->getStart(), $slot - 1, $slotRange->getConnection());
            }

            if (static::isValidRange($slot + 1, $slotRange->getEnd())) {
                $results[] = new SlotRange($slot + 1, $slotRange->getEnd(), $slotRange->getConnection());
            }
        }

        $this->slotRanges = $results;
    }

    




    #[ReturnTypeWillChange]
    public function count()
    {
        return array_sum(array_map(
            function ($slotRange) {
                return $slotRange->count();
            },
            $this->slotRanges
        ));
    }

    




    #[ReturnTypeWillChange]
    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }

    






    protected function findRangeBySlot(int $slot)
    {
        foreach ($this->slotRanges as $slotRange) {
            if ($slotRange->hasSlot($slot)) {
                return $slotRange;
            }
        }

        return false;
    }

    






    protected function getGaps(array $slotRanges)
    {
        if (empty($slotRanges)) {
            return [
                new NullSlotRange(0, SlotRange::MAX_SLOTS),
            ];
        }
        $gaps = [];
        $count = count($slotRanges);
        $i = 0;
        foreach ($slotRanges as $key => $slotRange) {
            $start = $slotRange->getStart();
            $end = $slotRange->getEnd();
            if (static::isValidRange($i, $start - 1)) {
                $gaps[] = new NullSlotRange($i, $start - 1);
            }

            $i = $end + 1;

            if ($key === $count - 1) {
                if (static::isValidRange($i, SlotRange::MAX_SLOTS)) {
                    $gaps[] = new NullSlotRange($i, SlotRange::MAX_SLOTS);
                }
            }
        }

        return $gaps;
    }

    






    protected function sortSlotRanges(array &$slotRanges)
    {
        usort(
            $slotRanges,
            function (SlotRange $a, SlotRange $b) {
                if ($a->getStart() == $b->getStart()) {
                    return 0;
                }

                return $a->getStart() < $b->getStart() ? -1 : 1;
            }
        );
    }

    






    protected function compactSlotRanges(array $slotRanges)
    {
        if (empty($slotRanges)) {
            return [];
        }

        $compacted = [];
        $count = count($slotRanges);
        $i = 0;
        $carry = $slotRanges[0];
        while ($i < $count) {
            $next = $slotRanges[$i + 1] ?? null;
            if (
                !is_null($next)
                && ($carry->getEnd() + 1) === $next->getStart()
                && $carry->getConnection() === $next->getConnection()
            ) {
                $carry = new SlotRange($carry->getStart(), $next->getEnd(), $carry->getConnection());
            } else {
                $compacted[] = $carry;
                $carry = $next;
            }
            $i++;
        }

        return array_values($compacted);
    }
}
