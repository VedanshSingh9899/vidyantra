<?php











namespace Predis\Connection;

use Predis\Command\CommandInterface;





interface ConnectionInterface
{
    


    public function connect();

    


    public function disconnect();

    




    public function isConnected();

    




    public function writeRequest(CommandInterface $command);

    






    public function readResponse(CommandInterface $command);

    






    public function write(string $buffer): void;

    







    public function executeCommand(CommandInterface $command);

    




    public function getParameters();
}
