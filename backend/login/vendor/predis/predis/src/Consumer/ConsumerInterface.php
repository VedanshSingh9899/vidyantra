<?php











namespace Predis\Consumer;

use Iterator;
use Predis\ClientInterface;
use ReturnTypeWillChange;

interface ConsumerInterface extends Iterator
{
    


    public function __construct(ClientInterface $client);

    





    public function stop(bool $drop = false): bool;

    




    public function getClient(): ClientInterface;

    




    #[ReturnTypeWillChange]
    public function current();

    




    #[ReturnTypeWillChange]
    public function next();

    




    #[ReturnTypeWillChange]
    public function valid();
}
