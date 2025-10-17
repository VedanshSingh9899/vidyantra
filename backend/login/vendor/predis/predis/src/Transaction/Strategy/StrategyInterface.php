<?php











namespace Predis\Transaction\Strategy;

use Predis\Command\CommandInterface;
use Predis\Transaction\Exception\TransactionException;

interface StrategyInterface
{
    




    public function initializeTransaction(): bool;

    






    public function executeCommand(CommandInterface $command);

    





    public function executeTransaction();

    




    public function multi();

    






    public function watch(array $keys);

    




    public function unwatch();

    




    public function discard();
}
