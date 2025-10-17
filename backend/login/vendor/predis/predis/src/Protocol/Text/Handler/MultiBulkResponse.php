<?php











namespace Predis\Protocol\Text\Handler;

use Predis\CommunicationException;
use Predis\Connection\CompositeConnectionInterface;
use Predis\Protocol\ProtocolException;







class MultiBulkResponse implements ResponseHandlerInterface
{
    


    public function handle(CompositeConnectionInterface $connection, $payload)
    {
        $length = (int) $payload;

        if ("$length" !== $payload) {
            CommunicationException::handle(new ProtocolException(
                $connection, "Cannot parse '$payload' as a valid length of a multi-bulk response [{$connection->getParameters()}]"
            ));
        }

        if ($length === -1) {
            return;
        }

        $list = [];

        if ($length > 0) {
            $handlersCache = [];
            $reader = $connection->getProtocol()->getResponseReader();

            for ($i = 0; $i < $length; ++$i) {
                $header = $connection->readLine();
                $prefix = $header[0];

                if (isset($handlersCache[$prefix])) {
                    $handler = $handlersCache[$prefix];
                } else {
                    $handler = $reader->getHandler($prefix);
                    $handlersCache[$prefix] = $handler;
                }

                $list[$i] = $handler->handle($connection, substr($header, 1));
            }
        }

        return $list;
    }
}
