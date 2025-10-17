<?php











namespace Predis\Command;












class RawFactory implements FactoryInterface
{
    


    public function supports(string ...$commandIDs): bool
    {
        return true;
    }

    


    public function create(string $commandID, array $arguments = []): CommandInterface
    {
        return new RawCommand($commandID, $arguments);
    }
}
