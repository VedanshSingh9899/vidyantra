<?php











namespace Predis\Protocol\Text\Handler;

use Predis\Connection\CompositeConnectionInterface;
use Predis\Response\Error;







class ErrorResponse implements ResponseHandlerInterface
{
    


    public function handle(CompositeConnectionInterface $connection, $payload)
    {
        return new Error($payload);
    }
}
