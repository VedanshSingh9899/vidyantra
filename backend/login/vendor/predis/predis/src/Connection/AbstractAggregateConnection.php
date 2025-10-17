<?php











namespace Predis\Connection;

use Predis\Command\Command;
use Predis\Command\CommandInterface;

abstract class AbstractAggregateConnection implements AggregateConnectionInterface
{
    


    abstract public function add(NodeConnectionInterface $connection);

    


    abstract public function remove(NodeConnectionInterface $connection);

    


    abstract public function getConnectionByCommand(CommandInterface $command);

    


    abstract public function getConnectionById($connectionID);

    


    abstract public function connect();

    


    abstract public function disconnect();

    


    abstract public function isConnected();

    


    abstract public function writeRequest(CommandInterface $command);

    


    abstract public function readResponse(CommandInterface $command);

    


    abstract public function executeCommand(CommandInterface $command);

    


    abstract public function getParameters();

    


    public function write(string $buffer): void
    {
        $rawCommands = [];
        $explodedBuffer = explode("\r\n", trim($buffer));

        while (!empty($explodedBuffer)) {
            $argsLen = (int) explode('*', $explodedBuffer[0])[1];
            $cmdLen = ($argsLen * 2) + 1;
            $rawCommands[] = array_splice($explodedBuffer, 0, $cmdLen);
        }

        foreach ($rawCommands as $command) {
            $command = implode("\r\n", $command) . "\r\n";
            $commandObj = Command::deserializeCommand($command);
            $this->getConnectionByCommand($commandObj)->write($command);
        }
    }
}
