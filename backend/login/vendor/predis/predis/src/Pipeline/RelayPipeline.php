<?php











namespace Predis\Pipeline;

use Predis\Connection\ConnectionInterface;
use Predis\Connection\RelayConnection;
use Predis\Response\Error;
use Predis\Response\ServerException;
use Relay\Exception as RelayException;
use SplQueue;

class RelayPipeline extends Pipeline
{
    







    protected function executePipeline(ConnectionInterface $connection, SplQueue $commands)
    {
        
        $client = $connection->getClient();

        $throw = $this->client->getOptions()->exceptions;

        try {
            $pipeline = $client->pipeline();

            foreach ($commands as $command) {
                $name = $command->getId();

                in_array($name, $connection->atypicalCommands)
                    ? $pipeline->{$name}(...$command->getArguments())
                    : $pipeline->rawCommand($name, ...$command->getArguments());
            }

            $responses = $pipeline->exec();

            if (!is_array($responses)) {
                return $responses;
            }

            foreach ($responses as $key => $response) {
                if ($response instanceof RelayException) {
                    if ($throw) {
                        throw $response;
                    }

                    $responses[$key] = new Error($response->getMessage());
                }
            }

            return $responses;
        } catch (RelayException $ex) {
            if ($client->getMode() !== $client::ATOMIC) {
                $client->discard();
            }

            throw new ServerException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
