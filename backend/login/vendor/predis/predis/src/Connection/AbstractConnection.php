<?php











namespace Predis\Connection;

use Predis\Command\CommandInterface;
use Predis\Command\RawCommand;
use Predis\CommunicationException;
use Predis\Connection\Resource\Exception\StreamInitException;
use Predis\Protocol\Parser\ParserStrategyResolver;
use Predis\Protocol\Parser\Strategy\ParserStrategyInterface;
use Predis\Protocol\ProtocolException;





abstract class AbstractConnection implements NodeConnectionInterface
{
    


    protected $parserStrategy;

    


    protected $clientId;

    protected $resource;
    private $cachedId;

    protected $parameters;

    


    protected $initCommands = [];

    


    public function __construct(ParametersInterface $parameters)
    {
        $this->parameters = $parameters;
        $this->setParserStrategy();
    }

    



    public function __destruct()
    {
        $this->disconnect();
    }

    


    public function isConnected()
    {
        return isset($this->resource);
    }

    


    public function hasDataToRead(): bool
    {
        return true;
    }

    





    abstract protected function createResource();

    


    public function connect()
    {
        if (!$this->isConnected()) {
            $this->resource = $this->createResource();

            return true;
        }

        return false;
    }

    


    public function disconnect()
    {
        unset($this->resource);
    }

    


    public function addConnectCommand(CommandInterface $command)
    {
        $this->initCommands[] = $command;
    }

    


    public function getInitCommands(): array
    {
        return $this->initCommands;
    }

    


    public function executeCommand(CommandInterface $command)
    {
        $this->writeRequest($command);

        return $this->readResponse($command);
    }

    


    public function readResponse(CommandInterface $command)
    {
        return $this->read();
    }

    






    protected function onConnectionError($message, $code = 0): void
    {
        CommunicationException::handle(
            new ConnectionException($this, "$message [{$this->getParameters()}]", $code)
        );
    }

    





    protected function onProtocolError($message)
    {
        CommunicationException::handle(
            new ProtocolException($this, "$message [{$this->getParameters()}]")
        );
    }

    


    public function getResource()
    {
        if (isset($this->resource)) {
            return $this->resource;
        }

        $this->connect();

        return $this->resource;
    }

    


    public function getParameters()
    {
        return $this->parameters;
    }

    




    protected function getIdentifier()
    {
        if ($this->parameters->scheme === 'unix') {
            return $this->parameters->path;
        }

        return "{$this->parameters->host}:{$this->parameters->port}";
    }

    


    public function getClientId(): ?int
    {
        return $this->clientId;
    }

    


    public function __toString()
    {
        if (!isset($this->cachedId)) {
            $this->cachedId = $this->getIdentifier();
        }

        return $this->cachedId;
    }

    


    public function __sleep()
    {
        return ['parameters', 'initCommands'];
    }

    




    protected function setParserStrategy(): void
    {
        $strategyResolver = new ParserStrategyResolver();
        $this->parserStrategy = $strategyResolver->resolve((int) $this->parameters->protocol);
    }
}
