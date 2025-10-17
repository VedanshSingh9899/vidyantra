<?php











namespace Predis\Collection\Iterator;

use Predis\ClientInterface;







class Keyspace extends CursorBasedIterator
{
    


    public function __construct(ClientInterface $client, $match = null, $count = null)
    {
        $this->requiredCommand($client, 'SCAN');

        parent::__construct($client, $match, $count);
    }

    


    protected function executeCommand()
    {
        return $this->client->scan($this->cursor, $this->getScanOptions());
    }
}
