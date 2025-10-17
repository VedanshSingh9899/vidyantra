<?php











namespace Predis\Consumer;

abstract class AbstractDispatcherLoop implements DispatcherLoopInterface
{
    


    protected $consumer;

    


    protected $defaultCallback;

    


    protected $callbacksDictionary;

    


    public function __construct(ConsumerInterface $consumer)
    {
        $this->consumer = $consumer;
    }

    


    public function getConsumer(): ConsumerInterface
    {
        return $this->consumer;
    }

    


    public function setDefaultCallback(?callable $callback = null): void
    {
        $this->defaultCallback = $callback;
    }

    


    public function attachCallback(string $messageType, callable $callback): void
    {
        $this->callbacksDictionary[$messageType] = $callback;
    }

    


    public function detachCallback(string $messageType): void
    {
        if (isset($this->callbacksDictionary[$messageType])) {
            unset($this->callbacksDictionary[$messageType]);
        }
    }

    


    abstract public function run(): void;

    


    public function stop(): void
    {
        $this->consumer->stop();
    }
}
