<?php











namespace Predis\Consumer;

use Predis\ClientInterface;
use ReturnTypeWillChange;

abstract class AbstractConsumer implements ConsumerInterface
{
    


    protected $client;

    


    protected $isValid = true;

    


    protected $position = 0;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    


    public function stop(bool $drop = false): bool
    {
        $this->isValid = false;

        if ($drop) {
            $this->client->disconnect();

            return true;
        }

        return true;
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    


    public function current()
    {
        return $this->getValue();
    }

    




    #[ReturnTypeWillChange]
    abstract protected function getValue();

    


    public function valid()
    {
        return $this->isValid;
    }

    


    public function next()
    {
        if ($this->valid()) {
            ++$this->position;
        }
    }

    


    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    


    #[ReturnTypeWillChange]
    public function rewind()
    {
        
    }
}
