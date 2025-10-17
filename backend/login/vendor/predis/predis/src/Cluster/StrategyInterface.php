<?php











namespace Predis\Cluster;

use Predis\Cluster\Distributor\DistributorInterface;
use Predis\Command\CommandInterface;







interface StrategyInterface
{
    







    public function getSlot(CommandInterface $command);

    







    public function getSlotByKey($key);

    




    public function getDistributor();

    





    public function checkSameSlotForKeys(array $keys): bool;
}
