<?php











namespace Predis\Command\Container;

interface ContainerInterface
{
    







    public function __call(string $subcommandID, array $arguments);

    




    public function getContainerCommandId(): string;
}
