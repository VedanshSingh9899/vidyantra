<?php











namespace Predis\Collection\Iterator;

use Iterator;
use Predis\ClientInterface;
use Predis\NotSupportedException;
use ReturnTypeWillChange;












abstract class CursorBasedIterator implements Iterator
{
    protected $client;
    protected $match;
    protected $count;

    protected $valid;
    protected $fetchmore;
    protected $elements;
    protected $cursor;
    protected $position;
    protected $current;

    




    public function __construct(ClientInterface $client, $match = null, $count = null)
    {
        $this->client = $client;
        $this->match = $match;
        $this->count = $count;

        $this->reset();
    }

    








    protected function requiredCommand(ClientInterface $client, $commandID)
    {
        if (!$client->getCommandFactory()->supports($commandID)) {
            throw new NotSupportedException("'$commandID' is not supported by the current command factory.");
        }
    }

    


    protected function reset()
    {
        $this->valid = true;
        $this->fetchmore = true;
        $this->elements = [];
        $this->cursor = 0;
        $this->position = -1;
        $this->current = null;
    }

    




    protected function getScanOptions()
    {
        $options = [];

        if (strlen(strval($this->match)) > 0) {
            $options['MATCH'] = $this->match;
        }

        if ($this->count > 0) {
            $options['COUNT'] = $this->count;
        }

        return $options;
    }

    





    abstract protected function executeCommand();

    



    protected function fetch()
    {
        [$cursor, $elements] = $this->executeCommand();

        if (!$cursor) {
            $this->fetchmore = false;
        }

        $this->cursor = $cursor;
        $this->elements = $elements;
    }

    


    protected function extractNext()
    {
        ++$this->position;
        $this->current = array_shift($this->elements);
    }

    


    #[ReturnTypeWillChange]
    public function rewind()
    {
        $this->reset();
        $this->next();
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
        tryFetch:
            if (!$this->elements && $this->fetchmore) {
                $this->fetch();
            }

        if ($this->elements) {
            $this->extractNext();
        } elseif ($this->cursor) {
            goto tryFetch;
        } else {
            $this->valid = false;
        }
    }

    


    #[ReturnTypeWillChange]
    public function valid()
    {
        return $this->valid;
    }
}
