<?php











namespace Predis\Consumer\Push;

use Predis\Consumer\AbstractDispatcherLoop;

class DispatcherLoop extends AbstractDispatcherLoop
{
    public function __construct(Consumer $consumer)
    {
        $this->consumer = $consumer;
    }

    


    public function run(): void
    {
        foreach ($this->consumer as $notification) {
            if (null !== $notification) {
                $messageType = $notification->getDataType();

                if (isset($this->callbacksDictionary[$messageType])) {
                    $callback = $this->callbacksDictionary[$messageType];
                    $callback($notification->getPayload(), $this);
                } elseif (isset($this->defaultCallback)) {
                    $callback = $this->defaultCallback;
                    $callback($notification->getPayload(), $this);
                }
            }
        }
    }
}
