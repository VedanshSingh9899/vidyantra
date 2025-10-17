<?php











namespace Predis\Connection;

trait RelayMethods
{
    





    public function onFlushed(?callable $callback)
    {
        return $this->client->onFlushed($callback);
    }

    






    public function onInvalidated(?callable $callback, ?string $pattern = null)
    {
        return $this->client->onInvalidated($callback, $pattern);
    }

    




    public function dispatchEvents()
    {
        return $this->client->dispatchEvents();
    }

    





    public function addIgnorePatterns(string ...$pattern)
    {
        return $this->client->addIgnorePatterns(...$pattern);
    }

    





    public function addAllowPatterns(string ...$pattern)
    {
        return $this->client->addAllowPatterns(...$pattern);
    }

    




    public function endpointId()
    {
        return $this->client->endpointId();
    }

    




    public function socketId()
    {
        return $this->client->socketId();
    }

    




    public function license()
    {
        return $this->client->license();
    }

    




    public function stats()
    {
        return $this->client->stats();
    }

    




    public function maxMemory()
    {
        return $this->client->maxMemory();
    }

    









    public function flushMemory(?string $endpointId = null, ?int $db = null)
    {
        return $this->client->flushMemory($endpointId, $db);
    }
}
