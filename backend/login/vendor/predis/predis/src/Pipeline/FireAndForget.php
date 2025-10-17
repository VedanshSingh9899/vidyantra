<?php











namespace Predis\Pipeline;

use Predis\Connection\ConnectionInterface;
use SplQueue;




class FireAndForget extends Pipeline
{
    


    protected function executePipeline(ConnectionInterface $connection, SplQueue $commands)
    {
        $buffer = '';

        while (!$commands->isEmpty()) {
            $buffer .= $commands->dequeue()->serializeCommand();
        }

        $connection->write($buffer);
        $connection->disconnect();

        return [];
    }
}
