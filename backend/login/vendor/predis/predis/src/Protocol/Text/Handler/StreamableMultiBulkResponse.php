<?php











namespace Predis\Protocol\Text\Handler;

use Predis\CommunicationException;
use Predis\Connection\CompositeConnectionInterface;
use Predis\Protocol\ProtocolException;
use Predis\Response\Iterator\MultiBulk as MultiBulkIterator;










class StreamableMultiBulkResponse implements ResponseHandlerInterface
{
    


    public function handle(CompositeConnectionInterface $connection, $payload)
    {
        $length = (int) $payload;

        if ("$length" != $payload) {
            CommunicationException::handle(new ProtocolException(
                $connection, "Cannot parse '$payload' as a valid length for a multi-bulk response [{$connection->getParameters()}]"
            ));
        }

        return new MultiBulkIterator($connection, $length);
    }
}
