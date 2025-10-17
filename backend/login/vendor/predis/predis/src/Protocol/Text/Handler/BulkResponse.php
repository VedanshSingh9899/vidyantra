<?php











namespace Predis\Protocol\Text\Handler;

use Predis\CommunicationException;
use Predis\Connection\CompositeConnectionInterface;
use Predis\Protocol\ProtocolException;







class BulkResponse implements ResponseHandlerInterface
{
    


    public function handle(CompositeConnectionInterface $connection, $payload)
    {
        $length = (int) $payload;

        if ("$length" !== $payload) {
            CommunicationException::handle(new ProtocolException(
                $connection, "Cannot parse '$payload' as a valid length for a bulk response [{$connection->getParameters()}]"
            ));
        }

        if ($length >= 0) {
            return substr($connection->readBuffer($length + 2), 0, -2);
        }

        if ($length == -1) {
            return;
        }

        CommunicationException::handle(new ProtocolException(
            $connection, "Value '$payload' is not a valid length for a bulk response [{$connection->getParameters()}]"
        ));

        return;
    }
}
