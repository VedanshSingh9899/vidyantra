<?php











namespace Predis\Cluster;

use Predis\Cluster\Distributor\DistributorInterface;
use Predis\Cluster\Distributor\HashRing;




class PredisStrategy extends ClusterStrategy
{
    protected $distributor;

    


    public function __construct(?DistributorInterface $distributor = null)
    {
        parent::__construct();

        $this->distributor = $distributor ?: new HashRing();
    }

    


    public function getSlotByKey($key)
    {
        $key = $this->extractKeyTag($key);
        $hash = $this->distributor->hash($key);

        return $this->distributor->getSlot($hash);
    }

    


    public function checkSameSlotForKeys(array $keys): bool
    {
        if (!$count = count($keys)) {
            return false;
        }

        $currentKey = $this->extractKeyTag($keys[0]);

        for ($i = 1; $i < $count; ++$i) {
            $nextKey = $this->extractKeyTag($keys[$i]);

            if ($currentKey !== $nextKey) {
                return false;
            }
        }

        return true;
    }

    


    public function getDistributor()
    {
        return $this->distributor;
    }
}
