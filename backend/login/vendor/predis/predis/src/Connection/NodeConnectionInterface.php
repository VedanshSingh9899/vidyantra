<?php











namespace Predis\Connection;

use Predis\Command\CommandInterface;




interface NodeConnectionInterface extends ConnectionInterface
{
    




    public function __toString();

    




    public function getResource();

    




    public function getParameters();

    




    public function getClientId(): ?int;

    





    public function addConnectCommand(CommandInterface $command);

    




    public function read();

    






    public function write(string $buffer): void;

    




    public function hasDataToRead(): bool;
}
