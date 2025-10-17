<?php











namespace Predis\Consumer;




interface DispatcherLoopInterface
{
    




    public function getConsumer(): ConsumerInterface;

    





    public function setDefaultCallback(?callable $callback = null): void;

    






    public function attachCallback(string $messageType, callable $callback): void;

    





    public function detachCallback(string $messageType): void;

    




    public function run(): void;

    




    public function stop(): void;
}
