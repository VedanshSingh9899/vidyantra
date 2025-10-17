<?php











namespace Predis\Consumer\PubSub;

use Predis\ClientException;
use Predis\ClientInterface;
use Predis\Command\Command;
use Predis\Connection\Cluster\ClusterInterface;
use Predis\Connection\ConnectionInterface;
use Predis\Connection\NodeConnectionInterface;
use Predis\Consumer\AbstractConsumer;
use Predis\NotSupportedException;




class Consumer extends AbstractConsumer
{
    public const SUBSCRIBE = 'subscribe';
    public const SSUBSCRIBE = 'ssubscribe';
    public const UNSUBSCRIBE = 'unsubscribe';
    public const SUNSUBSCRIBE = 'sunsubscribe';
    public const PSUBSCRIBE = 'psubscribe';
    public const PUNSUBSCRIBE = 'punsubscribe';
    public const MESSAGE = 'message';
    public const PMESSAGE = 'pmessage';
    public const PONG = 'pong';

    public const STATUS_VALID = 1;       
    public const STATUS_SUBSCRIBED = 2;  
    public const STATUS_PSUBSCRIBED = 4; 
    public const STATUS_SSUBSCRIBED = 8; 

    protected $statusFlags = self::STATUS_VALID;

    protected $options;

    


    private $subscriptionContext;

    




    public function __construct(ClientInterface $client, ?array $options = null)
    {
        $this->options = $options ?: [];
        $this->setSubscriptionContext($client->getConnection());

        parent::__construct($client);
        $this->checkCapabilities($client);

        $this->client = $client;

        $this->genericSubscribeInit('subscribe');
        $this->genericSubscribeInit('ssubscribe');
        $this->genericSubscribeInit('psubscribe');
    }

    




    public function getSubscriptionContext(): SubscriptionContext
    {
        return $this->subscriptionContext;
    }

    







    private function checkCapabilities(ClientInterface $client)
    {
        $commands = ['publish', 'spublish', 'subscribe', 'ssubscribe', 'unsubscribe', 'sunsubscribe', 'psubscribe', 'punsubscribe'];

        if (!$client->getCommandFactory()->supports(...$commands)) {
            throw new NotSupportedException(
                'PUB/SUB commands are not supported by the current command factory.'
            );
        }
    }

    




    private function genericSubscribeInit($subscribeAction)
    {
        if (isset($this->options[$subscribeAction])) {
            $this->$subscribeAction($this->options[$subscribeAction]);
        }
    }

    


    protected function writeRequest($method, $arguments)
    {
        $this->client->getConnection()->writeRequest(
            $this->client->createCommand($method,
                Command::normalizeArguments($arguments)
            )
        );
    }

    


    public function __destruct()
    {
        $this->stop(true);
    }

    






    protected function isFlagSet($value)
    {
        return ($this->statusFlags & $value) === $value;
    }

    




    public function subscribe(string ...$channels)
    {
        $this->writeRequest(self::SUBSCRIBE, func_get_args());
        $this->statusFlags |= self::STATUS_SUBSCRIBED;
    }

    




    public function ssubscribe(string ...$channels)
    {
        $this->writeRequest(self::SSUBSCRIBE, func_get_args());
        $this->statusFlags |= self::STATUS_SSUBSCRIBED;
    }

    




    public function unsubscribe(...$channel)
    {
        $this->writeRequest(self::UNSUBSCRIBE, func_get_args());
    }

    




    public function sunsubscribe(string ...$channels)
    {
        $this->writeRequest(self::SUNSUBSCRIBE, func_get_args());
    }

    




    public function psubscribe(...$pattern)
    {
        $this->writeRequest(self::PSUBSCRIBE, func_get_args());
        $this->statusFlags |= self::STATUS_PSUBSCRIBED;
    }

    




    public function punsubscribe(...$pattern)
    {
        $this->writeRequest(self::PUNSUBSCRIBE, func_get_args());
    }

    





    public function ping($payload = null)
    {
        $this->writeRequest('PING', [$payload]);
    }

    







    public function stop(bool $drop = false): bool
    {
        if (!$this->valid()) {
            return false;
        }

        if ($drop) {
            $this->invalidate();
            $this->disconnect();
        } else {
            if ($this->isFlagSet(self::STATUS_SUBSCRIBED)) {
                $this->unsubscribe();
            }
            if ($this->isFlagSet(self::STATUS_PSUBSCRIBED)) {
                $this->punsubscribe();
            }
            if ($this->isFlagSet(self::STATUS_SSUBSCRIBED)) {
                $this->sunsubscribe();
            }
        }

        return !$drop;
    }

    


    public function current()
    {
        return $this->getValue();
    }

    




    public function valid()
    {
        $isValid = $this->isFlagSet(self::STATUS_VALID);
        $subscriptionFlags = self::STATUS_SUBSCRIBED | self::STATUS_PSUBSCRIBED | self::STATUS_SSUBSCRIBED;
        $hasSubscriptions = ($this->statusFlags & $subscriptionFlags) > 0;

        return $isValid && $hasSubscriptions;
    }

    


    protected function invalidate()
    {
        $this->statusFlags = 0;    
    }

    


    protected function disconnect()
    {
        $this->client->disconnect();
    }

    


    protected function getValue()
    {
        
        $connection = $this->client->getConnection();
        $response = $connection->read();

        switch ($response[0]) {
            case self::SUBSCRIBE:
            case self::SSUBSCRIBE:
            case self::UNSUBSCRIBE:
            case self::SUNSUBSCRIBE:
            case self::PSUBSCRIBE:
            case self::PUNSUBSCRIBE:
                if ($response[2] === 0) {
                    $this->invalidate();
                }
                
                
                

            case self::MESSAGE:
                return (object) [
                    'kind' => $response[0],
                    'channel' => $response[1],
                    'payload' => $response[2],
                ];

            case self::PMESSAGE:
                return (object) [
                    'kind' => $response[0],
                    'pattern' => $response[1],
                    'channel' => $response[2],
                    'payload' => $response[3],
                ];

            case self::PONG:
                return (object) [
                    'kind' => $response[0],
                    'payload' => $response[1],
                ];

            default:
                throw new ClientException(
                    "Unknown message type '{$response[0]}' received in the PUB/SUB context."
                );
        }
    }

    





    private function setSubscriptionContext(ConnectionInterface $connection): void
    {
        if ($connection instanceof ClusterInterface) {
            $this->subscriptionContext = new SubscriptionContext(SubscriptionContext::CONTEXT_SHARDED);
        } else {
            $this->subscriptionContext = new SubscriptionContext();
        }
    }
}
