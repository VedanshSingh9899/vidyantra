<?php











namespace Predis\Pipeline;

use Exception;
use InvalidArgumentException;
use Predis\ClientContextInterface;
use Predis\ClientException;
use Predis\ClientInterface;
use Predis\Command\CommandInterface;
use Predis\Connection\ConnectionInterface;
use Predis\Connection\Replication\ReplicationInterface;
use Predis\Response\ErrorInterface as ErrorResponseInterface;
use Predis\Response\ResponseInterface;
use Predis\Response\ServerException;
use SplQueue;







class Pipeline implements ClientContextInterface
{
    protected $client;
    private $pipeline;

    private $responses = [];
    private $running = false;

    


    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $this->pipeline = new SplQueue();
    }

    







    public function __call($method, $arguments)
    {
        $command = $this->client->createCommand($method, $arguments);
        $this->recordCommand($command);

        return $this;
    }

    




    protected function recordCommand(CommandInterface $command)
    {
        $this->pipeline->enqueue($command);
    }

    






    public function executeCommand(CommandInterface $command)
    {
        $this->recordCommand($command);

        return $this;
    }

    







    protected function exception(ConnectionInterface $connection, ErrorResponseInterface $response)
    {
        $connection->disconnect();
        $message = $response->getMessage();

        throw new ServerException($message);
    }

    




    protected function getConnection()
    {
        $connection = $this->getClient()->getConnection();

        if ($connection instanceof ReplicationInterface) {
            $connection->switchToMaster();
        }

        return $connection;
    }

    








    protected function executePipeline(ConnectionInterface $connection, SplQueue $commands)
    {
        $buffer = '';

        foreach ($commands as $command) {
            $buffer .= $command->serializeCommand();
        }

        $connection->write($buffer);

        $responses = [];
        $exceptions = $this->throwServerExceptions();
        $protocolVersion = (int) $connection->getParameters()->protocol;

        while (!$commands->isEmpty()) {
            $command = $commands->dequeue();
            $response = $connection->readResponse($command);

            if (!$response instanceof ResponseInterface) {
                if ($protocolVersion === 2) {
                    $responses[] = $command->parseResponse($response);
                } else {
                    $responses[] = $command->parseResp3Response($response);
                }
            } elseif ($response instanceof ErrorResponseInterface && $exceptions) {
                $this->exception($connection, $response);
            } else {
                $responses[] = $response;
            }
        }

        return $responses;
    }

    






    public function flushPipeline($send = true)
    {
        if ($send && !$this->pipeline->isEmpty()) {
            $responses = $this->executePipeline($this->getConnection(), $this->pipeline);
            $this->responses = array_merge($this->responses, $responses);
        } else {
            $this->pipeline = new SplQueue();
        }

        return $this;
    }

    






    private function setRunning($bool)
    {
        if ($bool && $this->running) {
            throw new ClientException('The current pipeline context is already being executed.');
        }

        $this->running = $bool;
    }

    








    public function execute($callable = null)
    {
        if ($callable && !is_callable($callable)) {
            throw new InvalidArgumentException('The argument must be a callable object.');
        }

        $exception = null;
        $this->setRunning(true);

        try {
            if ($callable) {
                call_user_func($callable, $this);
            }

            $this->flushPipeline();
        } catch (Exception $exception) {
            
        }

        $this->setRunning(false);

        if ($exception) {
            throw $exception;
        }

        return $this->responses;
    }

    




    protected function throwServerExceptions()
    {
        return (bool) $this->client->getOptions()->exceptions;
    }

    




    public function getClient()
    {
        return $this->client;
    }
}
