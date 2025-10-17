<?php











namespace Predis\Protocol\Text;

use Predis\CommunicationException;
use Predis\Connection\CompositeConnectionInterface;
use Predis\Protocol\ProtocolException;
use Predis\Protocol\ResponseReaderInterface;






class ResponseReader implements ResponseReaderInterface
{
    protected $handlers;

    public function __construct()
    {
        $this->handlers = $this->getDefaultHandlers();
    }

    




    protected function getDefaultHandlers()
    {
        return [
            '+' => new Handler\StatusResponse(),
            '-' => new Handler\ErrorResponse(),
            ':' => new Handler\IntegerResponse(),
            '$' => new Handler\BulkResponse(),
            '*' => new Handler\MultiBulkResponse(),
        ];
    }

    





    public function setHandler($prefix, Handler\ResponseHandlerInterface $handler)
    {
        $this->handlers[$prefix] = $handler;
    }

    






    public function getHandler($prefix)
    {
        if (isset($this->handlers[$prefix])) {
            return $this->handlers[$prefix];
        }

        return;
    }

    


    public function read(CompositeConnectionInterface $connection)
    {
        $header = $connection->readLine();

        if ($header === '') {
            $this->onProtocolError($connection, 'Unexpected empty response header');
        }

        $prefix = $header[0];

        if (!isset($this->handlers[$prefix])) {
            $this->onProtocolError($connection, "Unknown response prefix: '$prefix'");
        }

        return $this->handlers[$prefix]->handle($connection, substr($header, 1));
    }

    






    protected function onProtocolError(CompositeConnectionInterface $connection, $message)
    {
        CommunicationException::handle(
            new ProtocolException($connection, "$message [{$connection->getParameters()}]")
        );
    }
}
