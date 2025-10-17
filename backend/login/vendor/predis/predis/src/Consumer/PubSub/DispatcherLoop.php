<?php











namespace Predis\Consumer\PubSub;

use Predis\Command\Processor\KeyPrefixProcessor;
use Predis\Consumer\AbstractDispatcherLoop;





class DispatcherLoop extends AbstractDispatcherLoop
{
    


    protected $consumer;

    public function __construct(Consumer $consumer)
    {
        $this->consumer = $consumer;
    }

    





    public function attachCallback(string $messageType, callable $callback): void
    {
        $callbackName = $this->getPrefixKeys() . $messageType;

        $this->callbacksDictionary[$callbackName] = $callback;

        if ($this->consumer->getSubscriptionContext()->getContext() === SubscriptionContext::CONTEXT_SHARDED) {
            $this->consumer->ssubscribe($messageType);
        } else {
            $this->consumer->subscribe($messageType);
        }
    }

    




    public function detachCallback(string $messageType): void
    {
        $callbackName = $this->getPrefixKeys() . $messageType;

        if (isset($this->callbacksDictionary[$callbackName])) {
            unset($this->callbacksDictionary[$callbackName]);

            if ($this->consumer->getSubscriptionContext()->getContext() === SubscriptionContext::CONTEXT_SHARDED) {
                $this->consumer->sunsubscribe($messageType);
            } else {
                $this->consumer->unsubscribe($messageType);
            }
        }
    }

    


    public function run(): void
    {
        foreach ($this->consumer as $message) {
            $kind = $message->kind;

            if ($kind !== Consumer::MESSAGE && $kind !== Consumer::PMESSAGE) {
                if (isset($this->defaultCallback)) {
                    $callback = $this->defaultCallback;
                    $callback($message, $this);
                }

                continue;
            }

            if (isset($this->callbacksDictionary[$message->channel])) {
                $callback = $this->callbacksDictionary[$message->channel];
                $callback($message->payload, $this);
            } elseif (isset($this->defaultCallback)) {
                $callback = $this->defaultCallback;
                $callback($message, $this);
            }
        }
    }

    




    protected function getPrefixKeys(): string
    {
        $options = $this->consumer->getClient()->getOptions();

        if (isset($options->prefix)) {
            
            $processor = $options->prefix;

            return $processor->getPrefix();
        }

        return '';
    }
}
