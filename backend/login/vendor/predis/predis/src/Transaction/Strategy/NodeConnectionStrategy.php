<?php











namespace Predis\Transaction\Strategy;

use Predis\Connection\NodeConnectionInterface;
use Predis\Transaction\MultiExecState;

class NodeConnectionStrategy extends NonClusterConnectionStrategy
{
    


    protected $connection;

    public function __construct(NodeConnectionInterface $connection, MultiExecState $state)
    {
        parent::__construct($connection, $state);
    }
}
