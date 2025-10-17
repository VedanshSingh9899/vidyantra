<?php











namespace Predis\Consumer\Push;

use Predis\Response\ResponseInterface;

interface PushResponseInterface extends ResponseInterface
{
    public const PUB_SUB_DATA_TYPE = 'pubsub';
    public const MONITOR_DATA_TYPE = 'monitor';
    public const INVALIDATE_DATA_TYPE = 'invalidate';
    public const MESSAGE_DATA_TYPE = 'message';

    




    public function getDataType(): string;

    




    public function getPayload(): array;
}
