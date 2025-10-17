<?php











namespace Predis\Consumer\PubSub;

class SubscriptionContext
{
    public const CONTEXT_SHARDED = 'sharded';
    public const CONTEXT_NON_SHARDED = 'non_sharded';

    


    private $context;

    public function __construct(string $context = self::CONTEXT_NON_SHARDED)
    {
        $this->context = $context;
    }

    


    public function getContext(): string
    {
        return $this->context;
    }
}
