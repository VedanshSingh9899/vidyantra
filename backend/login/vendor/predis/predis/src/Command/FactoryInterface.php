<?php











namespace Predis\Command;








interface FactoryInterface
{
    






    public function supports(string ...$commandIDs): bool;

    







    public function create(string $commandID, array $arguments = []): CommandInterface;
}
