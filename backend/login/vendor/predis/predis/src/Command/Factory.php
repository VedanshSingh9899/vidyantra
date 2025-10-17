<?php











namespace Predis\Command;

use InvalidArgumentException;
use Predis\ClientException;
use Predis\Command\Processor\ProcessorInterface;








abstract class Factory implements FactoryInterface
{
    protected $commands = [];
    protected $processor;

    


    public function supports(string ...$commandIDs): bool
    {
        foreach ($commandIDs as $commandID) {
            if ($this->getCommandClass($commandID) === null) {
                return false;
            }
        }

        return true;
    }

    








    public function getCommandClass(string $commandID): ?string
    {
        return $this->commands[strtoupper($commandID)] ?? null;
    }

    


    public function create(string $commandID, array $arguments = []): CommandInterface
    {
        if (!$commandClass = $this->getCommandClass($commandID)) {
            $commandID = strtoupper($commandID);

            throw new ClientException("Command `$commandID` is not a registered Redis command.");
        }

        $command = new $commandClass();
        $command->setArguments($arguments);

        if (isset($this->processor)) {
            $this->processor->process($command);
        }

        return $command;
    }

    











    public function define(string $commandID, string $commandClass): void
    {
        if (!is_a($commandClass, 'Predis\Command\CommandInterface', true)) {
            throw new InvalidArgumentException(
                "Class $commandClass must implement Predis\Command\CommandInterface"
            );
        }

        $this->commands[strtoupper($commandID)] = $commandClass;
    }

    








    public function undefine(string $commandID): void
    {
        unset($this->commands[strtoupper($commandID)]);
    }

    











    public function setProcessor(?ProcessorInterface $processor): void
    {
        $this->processor = $processor;
    }

    




    public function getProcessor(): ?ProcessorInterface
    {
        return $this->processor;
    }
}
