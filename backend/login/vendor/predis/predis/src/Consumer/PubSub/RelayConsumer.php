<?php











namespace Predis\Consumer\PubSub;

use Predis\NotSupportedException;




class RelayConsumer extends Consumer
{
    





    public function subscribe(string ...$channel) 
    {
        $channels = func_get_args();
        $callback = array_pop($channels);

        $this->statusFlags |= self::STATUS_SUBSCRIBED;

        $command = $this->client->createCommand('subscribe', [
            $channels,
            function ($relay, $channel, $message) use ($callback) {
                $callback((object) [
                    'kind' => is_null($message) ? self::SUBSCRIBE : self::MESSAGE,
                    'channel' => $channel,
                    'payload' => $message,
                ], $relay);
            },
        ]);

        $this->client->getConnection()->executeCommand($command);

        $this->invalidate();
    }

    





    public function psubscribe(...$pattern) 
    {
        $patterns = func_get_args();
        $callback = array_pop($patterns);

        $this->statusFlags |= self::STATUS_PSUBSCRIBED;

        $command = $this->client->createCommand('psubscribe', [
            $patterns,
            function ($relay, $pattern, $channel, $message) use ($callback) {
                $callback((object) [
                    'kind' => is_null($message) ? self::PSUBSCRIBE : self::PMESSAGE,
                    'pattern' => $pattern,
                    'channel' => $channel,
                    'payload' => $message,
                ], $relay);
            },
        ]);

        $this->client->getConnection()->executeCommand($command);

        $this->invalidate();
    }

    


    protected function genericSubscribeInit($subscribeAction)
    {
        if (isset($this->options[$subscribeAction])) {
            throw new NotSupportedException('Relay does not support Pub/Sub constructor options.');
        }
    }

    


    public function ping($payload = null)
    {
        throw new NotSupportedException('Relay does not support PING in Pub/Sub.');
    }

    


    public function stop($drop = false): bool
    {
        return false;
    }
}
