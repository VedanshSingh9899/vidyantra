<?php











namespace Predis\Cluster\Distributor;

use Predis\Cluster\Hash\HashGeneratorInterface;







class HashRing implements DistributorInterface, HashGeneratorInterface
{
    public const DEFAULT_REPLICAS = 128;
    public const DEFAULT_WEIGHT = 100;

    private $ring;
    private $ringKeys;
    private $ringKeysCount;
    private $replicas;
    private $nodeHashCallback;
    private $nodes = [];

    



    public function __construct($replicas = self::DEFAULT_REPLICAS, $nodeHashCallback = null)
    {
        $this->replicas = $replicas;
        $this->nodeHashCallback = $nodeHashCallback;
    }

    





    public function add($node, $weight = null)
    {
        
        
        $this->nodes[] = [
            'object' => $node,
            'weight' => (int) $weight ?: $this::DEFAULT_WEIGHT,
        ];

        $this->reset();
    }

    


    public function remove($node)
    {
        
        
        
        
        for ($i = 0; $i < count($this->nodes); ++$i) {
            if ($this->nodes[$i]['object'] === $node) {
                array_splice($this->nodes, $i, 1);
                $this->reset();

                break;
            }
        }
    }

    


    private function reset()
    {
        unset(
            $this->ring,
            $this->ringKeys,
            $this->ringKeysCount
        );
    }

    




    private function isInitialized()
    {
        return isset($this->ringKeys);
    }

    




    private function computeTotalWeight()
    {
        $totalWeight = 0;

        foreach ($this->nodes as $node) {
            $totalWeight += $node['weight'];
        }

        return $totalWeight;
    }

    


    private function initialize()
    {
        if ($this->isInitialized()) {
            return;
        }

        if (!$this->nodes) {
            throw new EmptyRingException('Cannot initialize an empty hashring.');
        }

        $this->ring = [];
        $totalWeight = $this->computeTotalWeight();
        $nodesCount = count($this->nodes);

        foreach ($this->nodes as $node) {
            $weightRatio = $node['weight'] / $totalWeight;
            $this->addNodeToRing($this->ring, $node, $nodesCount, $this->replicas, $weightRatio);
        }

        ksort($this->ring, SORT_NUMERIC);
        $this->ringKeys = array_keys($this->ring);
        $this->ringKeysCount = count($this->ringKeys);
    }

    








    protected function addNodeToRing(&$ring, $node, $totalNodes, $replicas, $weightRatio)
    {
        $nodeObject = $node['object'];
        $nodeHash = $this->getNodeHash($nodeObject);
        $replicas = (int) round($weightRatio * $totalNodes * $replicas);

        for ($i = 0; $i < $replicas; ++$i) {
            $key = $this->hash("$nodeHash:$i");
            $ring[$key] = $nodeObject;
        }
    }

    


    protected function getNodeHash($nodeObject)
    {
        if (!isset($this->nodeHashCallback)) {
            return (string) $nodeObject;
        }

        return call_user_func($this->nodeHashCallback, $nodeObject);
    }

    


    public function hash($value)
    {
        return crc32($value);
    }

    


    public function getByHash($hash)
    {
        return $this->ring[$this->getSlot($hash)];
    }

    


    public function getBySlot($slot)
    {
        $this->initialize();

        if (isset($this->ring[$slot])) {
            return $this->ring[$slot];
        }
    }

    


    public function getSlot($hash)
    {
        $this->initialize();

        $ringKeys = $this->ringKeys;
        $upper = $this->ringKeysCount - 1;
        $lower = 0;

        while ($lower <= $upper) {
            $index = ($lower + $upper) >> 1;
            $item = $ringKeys[$index];

            if ($item > $hash) {
                $upper = $index - 1;
            } elseif ($item < $hash) {
                $lower = $index + 1;
            } else {
                return $item;
            }
        }

        return $ringKeys[$this->wrapAroundStrategy($upper, $lower, $this->ringKeysCount)];
    }

    


    public function get($value)
    {
        $hash = $this->hash($value);

        return $this->getByHash($hash);
    }

    








    protected function wrapAroundStrategy($upper, $lower, $ringKeysCount)
    {
        
        
        return $upper >= 0 ? $upper : $ringKeysCount - 1;
    }

    


    public function getHashGenerator()
    {
        return $this;
    }
}
