<?php











namespace Predis\Connection\Cluster;

use Predis\Cluster\StrategyInterface;
use Predis\Command\CommandInterface;
use Predis\Connection\AggregateConnectionInterface;





interface ClusterInterface extends AggregateConnectionInterface
{
    





    public function executeCommandOnEachNode(CommandInterface $command): array;

    





    public function getClusterStrategy(): StrategyInterface;
}
