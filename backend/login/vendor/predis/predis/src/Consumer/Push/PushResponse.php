<?php











namespace Predis\Consumer\Push;

use ArrayAccess;
use ReturnTypeWillChange;

class PushResponse implements PushResponseInterface, ArrayAccess
{
    


    private $response;

    public function __construct(array $serverResponse)
    {
        $this->response = $serverResponse;
    }

    



    public function getDataType(): string
    {
        if (!isset($this->response[0])) {
            throw new PushNotificationException('Invalid server response');
        }

        return $this->response[0];
    }

    


    public function getPayload(): array
    {
        return array_slice($this->response, 1);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->response[$offset]);
    }

    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->response[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->response[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->response[$offset]);
    }
}
