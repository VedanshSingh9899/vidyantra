<?php











namespace Predis\Connection;

use InvalidArgumentException;
use Predis\Command\CommandInterface;
use Predis\Protocol\ProtocolProcessorInterface;
use Predis\Protocol\Text\ProtocolProcessor as TextProtocolProcessor;
use Psr\Http\Message\StreamInterface;
use RuntimeException;







class CompositeStreamConnection extends StreamConnection implements CompositeConnectionInterface
{
    protected $protocol;

    



    public function __construct(
        ParametersInterface $parameters,
        ?ProtocolProcessorInterface $protocol = null
    ) {
        parent::__construct($parameters);
        $this->protocol = $protocol ?: new TextProtocolProcessor();
    }

    


    public function getProtocol()
    {
        return $this->protocol;
    }

    


    public function writeBuffer($buffer)
    {
        $this->write($buffer);
    }

    


    public function readBuffer($length)
    {
        if ($length <= 0) {
            throw new InvalidArgumentException('Length parameter must be greater than 0.');
        }

        $value = '';
        $stream = $this->getResource();

        if ($stream->eof()) {
            $this->onStreamError(new RuntimeException('Stream is already at the end'), '');
        }

        do {
            try {
                $chunk = $stream->read($length);
            } catch (RuntimeException $e) {
                $this->onStreamError($e, 'Error while reading bytes from the server.');
            }

            $value .= $chunk; 
        } while (($length -= strlen($chunk)) > 0); 

        return $value;
    }

    


    public function readLine()
    {
        $value = '';
        $stream = $this->getResource();

        if ($stream->eof()) {
            $this->onStreamError(new RuntimeException('Stream is already at the end'), '');
        }

        do {
            try {
                $chunk = $stream->read(-1);
            } catch (RuntimeException $e) {
                $this->onStreamError($e, 'Error while reading bytes from the server.');
            }

            $value .= $chunk; 
        } while (substr($value, -2) !== "\r\n");

        return substr($value, 0, -2);
    }

    


    public function writeRequest(CommandInterface $command)
    {
        $this->protocol->write($this, $command);
    }

    


    public function read()
    {
        return $this->protocol->read($this);
    }

    


    public function __sleep()
    {
        return array_merge(parent::__sleep(), ['protocol']);
    }
}
