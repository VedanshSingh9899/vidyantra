<?php











namespace Predis\Protocol\Text\Handler;

use Predis\CommunicationException;
use Predis\Connection\CompositeConnectionInterface;
use Predis\Protocol\ProtocolException;







class IntegerResponse implements ResponseHandlerInterface
{
    


    public function handle(CompositeConnectionInterface $connection, $payload)
    {
        if (is_numeric($payload)) {
            $integer = (int) $payload;

            return $integer == $payload ? $integer : $payload;
        }

        if ($payload !== 'nil') {
            CommunicationException::handle(new ProtocolException(
                $connection, "Cannot parse '$payload' as a valid numeric response [{$connection->getParameters()}]"
            ));
        }

        return;
    }
}
