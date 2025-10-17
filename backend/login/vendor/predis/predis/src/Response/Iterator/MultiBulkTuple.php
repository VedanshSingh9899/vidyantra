<?php











namespace Predis\Response\Iterator;

use InvalidArgumentException;
use OuterIterator;
use ReturnTypeWillChange;
use UnexpectedValueException;








class MultiBulkTuple extends MultiBulk implements OuterIterator
{
    private $iterator;

    


    public function __construct(MultiBulk $iterator)
    {
        $this->checkPreconditions($iterator);

        $this->size = count($iterator) / 2;
        $this->iterator = $iterator;
        $this->position = $iterator->getPosition();
        $this->current = $this->size > 0 ? $this->getValue() : null;
    }

    







    protected function checkPreconditions(MultiBulk $iterator)
    {
        if ($iterator->getPosition() !== 0) {
            throw new InvalidArgumentException(
                'Cannot initialize a tuple iterator using an already initiated iterator.'
            );
        }

        if (($size = count($iterator)) % 2 !== 0) {
            throw new UnexpectedValueException('Invalid response size for a tuple iterator.');
        }
    }

    


    #[ReturnTypeWillChange]
    public function getInnerIterator()
    {
        return $this->iterator;
    }

    


    public function __destruct()
    {
        $this->iterator->drop(true);
    }

    


    protected function getValue()
    {
        $k = $this->iterator->current();
        $this->iterator->next();

        $v = $this->iterator->current();
        $this->iterator->next();

        return [$k, $v];
    }
}
