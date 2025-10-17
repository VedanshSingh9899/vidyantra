<?php











namespace Predis\Cluster;

use Predis\Cluster\Hash\CRC16;
use Predis\Cluster\Hash\HashGeneratorInterface;
use Predis\NotSupportedException;





class RedisStrategy extends ClusterStrategy
{
    protected $hashGenerator;

    


    public function __construct(?HashGeneratorInterface $hashGenerator = null)
    {
        parent::__construct();

        $this->hashGenerator = $hashGenerator ?: new CRC16();
    }

    


    public function getSlotByKey($key)
    {
        $key = $this->extractKeyTag($key);

        return $this->hashGenerator->hash($key) & 0x3FFF;
    }

    


    public function getDistributor()
    {
        $class = get_class($this);
        throw new NotSupportedException("$class does not provide an external distributor");
    }
}
