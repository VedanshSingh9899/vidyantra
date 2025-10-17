<?php











namespace Predis\Monitor;

use Iterator;
use Predis\ClientInterface;
use Predis\Connection\Cluster\ClusterInterface;
use Predis\NotSupportedException;
use ReturnTypeWillChange;




class Consumer implements Iterator
{
    private $client;
    private $valid;
    private $position;

    


    public function __construct(ClientInterface $client)
    {
        $this->assertClient($client);

        $this->client = $client;

        $this->start();
    }

    


    public function __destruct()
    {
        $this->stop();
    }

    







    private function assertClient(ClientInterface $client)
    {
        if ($client->getConnection() instanceof ClusterInterface) {
            throw new NotSupportedException(
                'Cannot initialize a monitor consumer over cluster connections.'
            );
        }

        if (!$client->getCommandFactory()->supports('MONITOR')) {
            throw new NotSupportedException("'MONITOR' is not supported by the current command factory.");
        }
    }

    


    protected function start()
    {
        $this->client->executeCommand(
            $this->client->createCommand('MONITOR')
        );
        $this->valid = true;
    }

    



    public function stop()
    {
        $this->client->disconnect();
        $this->valid = false;
    }

    


    #[ReturnTypeWillChange]
    public function rewind()
    {
        
    }

    




    #[ReturnTypeWillChange]
    public function current()
    {
        return $this->getValue();
    }

    


    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    


    #[ReturnTypeWillChange]
    public function next()
    {
        ++$this->position;
    }

    




    #[ReturnTypeWillChange]
    public function valid()
    {
        return $this->valid;
    }

    





    private function getValue()
    {
        $database = 0;
        $client = null;
        $event = $this->client->getConnection()->read();

        $callback = function ($matches) use (&$database, &$client) {
            if (2 === $count = count($matches)) {
                
                $database = (int) $matches[1];
            }

            if (4 === $count) {
                
                $database = (int) $matches[2];
                $client = $matches[3];
            }

            return ' ';
        };

        $event = preg_replace_callback('/ \(db (\d+)\) | \[(\d+) (.*?)\] /', $callback, $event, 1);
        @[$timestamp, $command, $arguments] = explode(' ', $event, 3);

        return (object) [
            'timestamp' => (float) $timestamp,
            'database' => $database,
            'client' => $client,
            'command' => substr($command, 1, -1),
            'arguments' => $arguments,
        ];
    }
}
