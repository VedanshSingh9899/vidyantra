<?php











namespace Predis\Collection\Iterator;

use Predis\ClientInterface;







class HashKey extends CursorBasedIterator
{
    protected $key;

    


    public function __construct(ClientInterface $client, $key, $match = null, $count = null)
    {
        $this->requiredCommand($client, 'HSCAN');

        parent::__construct($client, $match, $count);

        $this->key = $key;
    }

    


    protected function executeCommand()
    {
        return $this->client->hscan($this->key, $this->cursor, $this->getScanOptions());
    }

    


    protected function extractNext()
    {
        $this->position = key($this->elements);
        $this->current = current($this->elements);

        unset($this->elements[$this->position]);
    }
}
