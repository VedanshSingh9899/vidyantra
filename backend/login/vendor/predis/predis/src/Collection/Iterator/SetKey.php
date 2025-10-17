<?php











namespace Predis\Collection\Iterator;

use Predis\ClientInterface;







class SetKey extends CursorBasedIterator
{
    protected $key;

    


    public function __construct(ClientInterface $client, $key, $match = null, $count = null)
    {
        $this->requiredCommand($client, 'SSCAN');

        parent::__construct($client, $match, $count);

        $this->key = $key;
    }

    


    protected function executeCommand()
    {
        return $this->client->sscan($this->key, $this->cursor, $this->getScanOptions());
    }
}
