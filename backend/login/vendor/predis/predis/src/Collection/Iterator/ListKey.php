<?php











namespace Predis\Collection\Iterator;

use InvalidArgumentException;
use Iterator;
use Predis\ClientInterface;
use Predis\NotSupportedException;
use ReturnTypeWillChange;













class ListKey implements Iterator
{
    protected $client;
    protected $count;
    protected $key;

    protected $valid;
    protected $fetchmore;
    protected $elements;
    protected $position;
    protected $current;

    






    public function __construct(ClientInterface $client, $key, $count = 10)
    {
        $this->requiredCommand($client, 'LRANGE');

        if ((false === $count = filter_var($count, FILTER_VALIDATE_INT)) || $count < 0) {
            throw new InvalidArgumentException('The $count argument must be a positive integer.');
        }

        $this->client = $client;
        $this->key = $key;
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
        $this->position = -1;
        $this->current = null;
    }

    





    protected function executeCommand()
    {
        return $this->client->lrange($this->key, $this->position + 1, $this->position + $this->count);
    }

    



    protected function fetch()
    {
        $elements = $this->executeCommand();

        if (count($elements) < $this->count) {
            $this->fetchmore = false;
        }

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
        if (!$this->elements && $this->fetchmore) {
            $this->fetch();
        }

        if ($this->elements) {
            $this->extractNext();
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
