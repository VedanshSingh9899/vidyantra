<?php











namespace Predis\Connection;

use Predis\Command\CommandInterface;
use Predis\Command\RawCommand;
use Predis\CommunicationException;
use Predis\Connection\Resource\Exception\StreamInitException;
use Predis\Connection\Resource\StreamFactory;
use Predis\Connection\Resource\StreamFactoryInterface;
use Predis\Consumer\Push\PushNotificationException;
use Predis\Consumer\Push\PushResponse;
use Predis\Protocol\Parser\Strategy\Resp2Strategy;
use Predis\Protocol\Parser\Strategy\Resp3Strategy;
use Predis\Protocol\Parser\UnexpectedTypeException;
use Predis\Response\Error;
use Predis\Response\ErrorInterface as ErrorResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;


















class StreamConnection extends AbstractConnection
{
    


    protected $streamFactory;

    



    public function __construct(ParametersInterface $parameters, ?StreamFactoryInterface $factory = null)
    {
        parent::__construct($parameters);
        $this->streamFactory = $factory ?? new StreamFactory();
    }

    




    public function __destruct()
    {
        if (isset($this->parameters->persistent) && $this->parameters->persistent) {
            return;
        }

        $this->disconnect();
    }

    


    protected function createResource(): StreamInterface
    {
        return $this->streamFactory->createStream($this->parameters);
    }

    


    public function connect()
    {
        if (parent::connect() && $this->initCommands) {
            foreach ($this->initCommands as $command) {
                $response = $this->executeCommand($command);

                $this->handleOnConnectResponse($response, $command);
            }
        }
    }

    


    public function disconnect()
    {
        if ($this->isConnected()) {
            $this->getResource()->close();

            parent::disconnect();
        }
    }

    



    public function write(string $buffer): void
    {
        $stream = $this->getResource();

        while (($length = strlen($buffer)) > 0) {
            try {
                $written = $stream->write($buffer);
            } catch (RuntimeException $e) {
                $this->onStreamError($e, 'Error while writing bytes to the server.');
            }

            if ($length === $written) { 
                return;
            }

            $buffer = substr($buffer, $written); 
        }
    }

    




    public function read()
    {
        $stream = $this->getResource();

        if ($stream->eof()) {
            $this->onStreamError(new RuntimeException('', 1), 'Stream is already at the end');
        }

        try {
            $chunk = $stream->read(-1);
        } catch (RuntimeException $e) {
            $this->onStreamError($e, 'Error while reading line from the server.');
        }

        try {
            $parsedData = $this->parserStrategy->parseData($chunk); 
        } catch (UnexpectedTypeException $e) {
            $this->onProtocolError("Unknown response prefix: '{$e->getType()}'.");

            return;
        }

        if (!is_array($parsedData)) {
            return $parsedData;
        }

        switch ($parsedData['type']) {
            case Resp3Strategy::TYPE_PUSH:
                $data = [];

                for ($i = 0; $i < $parsedData['value']; ++$i) {
                    $data[$i] = $this->read();
                }

                return new PushResponse($data);
            case Resp2Strategy::TYPE_ARRAY:
                $data = [];

                for ($i = 0; $i < $parsedData['value']; ++$i) {
                    $data[$i] = $this->read();
                }

                return $data;

            case Resp2Strategy::TYPE_BULK_STRING:
                $bulkData = $this->readByChunks($stream, $parsedData['value']);

                return substr($bulkData, 0, -2);

            case Resp3Strategy::TYPE_VERBATIM_STRING:
                $bulkData = $this->readByChunks($stream, $parsedData['value']);

                return substr($bulkData, $parsedData['offset'], -2);

            case Resp3Strategy::TYPE_BLOB_ERROR:
                $errorMessage = $this->readByChunks($stream, $parsedData['value']);

                return new Error(substr($errorMessage, 0, -2));

            case Resp3Strategy::TYPE_MAP:
                $data = [];

                for ($i = 0; $i < $parsedData['value']; ++$i) {
                    $key = $this->read();
                    $data[$key] = $this->read();
                }

                return $data;

            case Resp3Strategy::TYPE_SET:
                $data = [];

                for ($i = 0; $i < $parsedData['value']; ++$i) {
                    $element = $this->read();

                    if (!in_array($element, $data, true)) {
                        $data[] = $element;
                    }
                }

                return $data;
        }

        return $parsedData;
    }

    


    public function writeRequest(CommandInterface $command)
    {
        $buffer = $command->serializeCommand();
        $this->write($buffer);
    }

    


    public function hasDataToRead(): bool
    {
        return !$this->getResource()->eof();
    }

    







    private function readByChunks(StreamInterface $stream, int $chunkSize): string
    {
        $string = '';
        $bytesLeft = ($chunkSize += 2);

        do {
            try {
                $chunk = $stream->read(min($bytesLeft, 4096));
            } catch (RuntimeException $e) {
                $this->onStreamError($e, 'Error while reading bytes from the server.');
            }

            $string .= $chunk; 
            $bytesLeft = $chunkSize - strlen($string);
        } while ($bytesLeft > 0);

        return $string;
    }

    







    private function handleOnConnectResponse($response, CommandInterface $command): void
    {
        if ($response instanceof ErrorResponseInterface) {
            $this->handleError($response, $command);
        }

        if ($command->getId() === 'HELLO' && is_array($response)) {
            
            if (
                $this->getParameters()->protocol == 2
                && false !== $key = array_search('id', $response, true)
            ) {
                $this->clientId = $response[$key + 1];
            } elseif ($this->getParameters()->protocol == 3) {
                $this->clientId = $response['id'];
            }
        }
    }

    







    private function handleError(ErrorResponseInterface $error, CommandInterface $failedCommand): void
    {
        if ($failedCommand->getId() === 'CLIENT') {
            
            return;
        }

        if ($failedCommand->getId() === 'HELLO') {
            if (in_array('AUTH', $failedCommand->getArguments(), true)) {
                $parameters = $this->getParameters();

                
                $auth = new RawCommand('AUTH', [$parameters->password]);
                $response = $this->executeCommand($auth);

                if ($response instanceof ErrorResponseInterface) {
                    $this->onConnectionError("Failed: {$response->getMessage()}");
                }
            }

            $setName = new RawCommand('CLIENT', ['SETNAME', 'predis']);
            $response = $this->executeCommand($setName);
            $this->handleOnConnectResponse($response, $setName);

            return;
        }

        $this->onConnectionError("Failed: {$error->getMessage()}");
    }

    






    protected function onStreamError(RuntimeException $e, ?string $message = null)
    {
        
        if ($e->getCode() === 1) {
            $this->onConnectionError($message);
        }

        throw $e;
    }
}
