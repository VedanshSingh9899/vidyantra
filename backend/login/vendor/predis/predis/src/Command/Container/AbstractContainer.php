<?php











namespace Predis\Command\Container;

use Predis\ClientInterface;

abstract class AbstractContainer implements ContainerInterface
{
    


    protected $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    


    public function __call(string $subcommandID, array $arguments)
    {
        array_unshift($arguments, strtoupper($subcommandID));

        return $this->client->executeCommand(
            $this->client->createCommand($this->getContainerCommandId(), $arguments)
        );
    }

    abstract public function getContainerCommandId(): string;
}
