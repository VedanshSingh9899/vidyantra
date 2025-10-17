<?php











namespace Predis\Transaction\Strategy;

use Predis\Connection\Replication\ReplicationInterface;
use Predis\Transaction\MultiExecState;

class ReplicationConnectionStrategy extends NonClusterConnectionStrategy
{
    


    protected $connection;

    public function __construct(ReplicationInterface $connection, MultiExecState $state)
    {
        parent::__construct($connection, $state);
    }
}
