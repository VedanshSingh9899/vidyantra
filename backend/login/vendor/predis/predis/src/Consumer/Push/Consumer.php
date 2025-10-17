<?php











namespace Predis\Consumer\Push;

use Predis\ClientInterface;
use Predis\Connection\NodeConnectionInterface;
use Predis\Consumer\AbstractConsumer;

class Consumer extends AbstractConsumer
{
    



    public function __construct(ClientInterface $client, ?callable $preLoopCallback = null)
    {
        parent::__construct($client);

        if (null !== $preLoopCallback) {
            $preLoopCallback($this->client);
        }
    }

    


    public function current(): ?PushResponseInterface
    {
        return parent::current();
    }

    




    protected function getValue(): ?PushResponseInterface
    {
        
        $connection = $this->client->getConnection();
        $response = $connection->read();

        return ($response instanceof PushResponse) ? $response : null;
    }
}
