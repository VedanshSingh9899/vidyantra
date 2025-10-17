<?php











namespace Predis\Response\Iterator;

use Countable;
use Iterator;
use Predis\Response\ResponseInterface;
use ReturnTypeWillChange;











abstract class MultiBulkIterator implements Iterator, Countable, ResponseInterface
{
    protected $current;
    protected $position;
    protected $size;

    


    #[ReturnTypeWillChange]
    public function rewind()
    {
        
    }

    


    #[ReturnTypeWillChange]
    public function current()
    {
        return $this->current;
    }

    


    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    


    #[ReturnTypeWillChange]
    public function next()
    {
        if (++$this->position < $this->size) {
            $this->current = $this->getValue();
        }
    }

    


    #[ReturnTypeWillChange]
    public function valid()
    {
        return $this->position < $this->size;
    }

    








    #[ReturnTypeWillChange]
    public function count()
    {
        return $this->size;
    }

    




    public function getPosition()
    {
        return $this->position;
    }

    


    abstract protected function getValue();
}
