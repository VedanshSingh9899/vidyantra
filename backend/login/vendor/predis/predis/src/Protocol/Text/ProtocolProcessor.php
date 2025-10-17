<?php











namespace Predis\Protocol\Text;

use Predis\Command\CommandInterface;
use Predis\CommunicationException;
use Predis\Connection\CompositeConnectionInterface;
use Predis\Protocol\ProtocolException;
use Predis\Protocol\ProtocolProcessorInterface;
use Predis\Response\Error as ErrorResponse;
use Predis\Response\Iterator\MultiBulk as MultiBulkIterator;
use Predis\Response\Status as StatusResponse;






class ProtocolProcessor implements ProtocolProcessorInterface
{
    protected $mbiterable;
    protected $serializer;

    public function __construct()
    {
        $this->mbiterable = false;
        $this->serializer = new RequestSerializer();
    }

    


    public function write(CompositeConnectionInterface $connection, CommandInterface $command)
    {
        $request = $this->serializer->serialize($command);
        $connection->writeBuffer($request);
    }

    


    public function read(CompositeConnectionInterface $connection)
    {
        $chunk = $connection->readLine();
        $prefix = $chunk[0];
        $payload = substr($chunk, 1);

        switch ($prefix) {
            case '+':
                return new StatusResponse($payload);

            case '$':
                $size = (int) $payload;
                if ($size === -1) {
                    return;
                }

                return substr($connection->readBuffer($size + 2), 0, -2);

            case '*':
                $count = (int) $payload;

                if ($count === -1) {
                    return;
                }
                if ($this->mbiterable) {
                    return new MultiBulkIterator($connection, $count);
                }

                $multibulk = [];

                for ($i = 0; $i < $count; ++$i) {
                    $multibulk[$i] = $this->read($connection);
                }

                return $multibulk;

            case ':':
                $integer = (int) $payload;

                return $integer == $payload ? $integer : $payload;

            case '-':
                return new ErrorResponse($payload);

            default:
                CommunicationException::handle(new ProtocolException(
                    $connection, "Unknown response prefix: '$prefix' [{$connection->getParameters()}]"
                ));

                return;
        }
    }

    










    public function useIterableMultibulk($value)
    {
        $this->mbiterable = (bool) $value;
    }
}
