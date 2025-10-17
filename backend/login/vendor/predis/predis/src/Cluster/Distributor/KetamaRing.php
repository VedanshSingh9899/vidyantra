<?php











namespace Predis\Cluster\Distributor;







class KetamaRing extends HashRing
{
    public const DEFAULT_REPLICAS = 160;

    


    public function __construct($nodeHashCallback = null)
    {
        parent::__construct($this::DEFAULT_REPLICAS, $nodeHashCallback);
    }

    


    protected function addNodeToRing(&$ring, $node, $totalNodes, $replicas, $weightRatio)
    {
        $nodeObject = $node['object'];
        $nodeHash = $this->getNodeHash($nodeObject);
        $replicas = (int) floor($weightRatio * $totalNodes * ($replicas / 4));

        for ($i = 0; $i < $replicas; ++$i) {
            $unpackedDigest = unpack('V4', md5("$nodeHash-$i", true));

            foreach ($unpackedDigest as $key) {
                $ring[$key] = $nodeObject;
            }
        }
    }

    


    public function hash($value)
    {
        $hash = unpack('V', md5($value, true));

        return $hash[1];
    }

    


    protected function wrapAroundStrategy($upper, $lower, $ringKeysCount)
    {
        
        
        return $lower < $ringKeysCount ? $lower : 0;
    }
}
