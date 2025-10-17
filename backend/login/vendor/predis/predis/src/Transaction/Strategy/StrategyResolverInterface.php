<?php











namespace Predis\Transaction\Strategy;

use Predis\Connection\ConnectionInterface;
use Predis\Transaction\MultiExecState;

interface StrategyResolverInterface
{
    






    public function resolve(ConnectionInterface $connection, MultiExecState $state): StrategyInterface;
}
