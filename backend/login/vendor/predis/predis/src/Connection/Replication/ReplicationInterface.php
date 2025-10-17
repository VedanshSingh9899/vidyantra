<?php











namespace Predis\Connection\Replication;

use Predis\Connection\AggregateConnectionInterface;
use Predis\Connection\NodeConnectionInterface;




interface ReplicationInterface extends AggregateConnectionInterface
{
    


    public function switchToMaster();

    


    public function switchToSlave();

    




    public function getCurrent();

    




    public function getMaster();

    




    public function getSlaves();
}
