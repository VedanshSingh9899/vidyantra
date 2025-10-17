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




class SimpleSlotMap implements ArrayAccess, IteratorAggregate, Countable
{
    private $slots = [];

    






    public static function isValid($slot)
    {
        return $slot >= 0x0000 && $slot <= 0x3FFF;
    }

    







    public static function isValidRange($first, $last)
    {
        return $first >= 0x0000 && $first <= 0x3FFF && $last >= 0x0000 && $last <= 0x3FFF && $first <= $last;
    }

    


    public function reset()
    {
        $this->slots = [];
    }

    




    public function isEmpty()
    {
        return empty($this->slots);
    }

    






    public function toArray()
    {
        return $this->slots;
    }

    




    public function getNodes()
    {
        return array_keys(array_flip($this->slots));
    }

    








    public function setSlots($first, $last, $connection)
    {
        if (!static::isValidRange($first, $last)) {
            throw new OutOfBoundsException("Invalid slot range $first-$last for `$connection`");
        }

        $this->slots += array_fill($first, $last - $first + 1, (string) $connection);
    }

    







    public function getSlots($first, $last)
    {
        if (!static::isValidRange($first, $last)) {
            throw new OutOfBoundsException("Invalid slot range $first-$last");
        }

        return array_intersect_key($this->slots, array_fill($first, $last - $first + 1, null));
    }

    






    #[ReturnTypeWillChange]
    public function offsetExists($slot)
    {
        return isset($this->slots[$slot]);
    }

    






    #[ReturnTypeWillChange]
    public function offsetGet($slot)
    {
        return $this->slots[$slot] ?? null;
    }

    







    #[ReturnTypeWillChange]
    public function offsetSet($slot, $connection)
    {
        if (!static::isValid($slot)) {
            throw new OutOfBoundsException("Invalid slot $slot for `$connection`");
        }

        $this->slots[(int) $slot] = (string) $connection;
    }

    






    #[ReturnTypeWillChange]
    public function offsetUnset($slot)
    {
        unset($this->slots[$slot]);
    }

    




    #[ReturnTypeWillChange]
    public function count()
    {
        return count($this->slots);
    }

    




    #[ReturnTypeWillChange]
    public function getIterator()
    {
        return new ArrayIterator($this->slots);
    }
}
