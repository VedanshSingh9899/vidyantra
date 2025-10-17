<?php











namespace Predis;

use Exception;
use Predis\Connection\NodeConnectionInterface;




abstract class CommunicationException extends PredisException
{
    private $connection;

    





    public function __construct(
        NodeConnectionInterface $connection,
        $message = '',
        $code = 0,
        ?Exception $innerException = null
    ) {
        parent::__construct(
            is_null($message) ? '' : $message,
            is_null($code) ? 0 : $code,
            $innerException
        );

        $this->connection = $connection;
    }

    




    public function getConnection()
    {
        return $this->connection;
    }

    




    public function shouldResetConnection()
    {
        return true;
    }

    






    public static function handle(CommunicationException $exception)
    {
        if ($exception->shouldResetConnection()) {
            $connection = $exception->getConnection();

            if ($connection->isConnected()) {
                $connection->disconnect();
            }
        }

        throw $exception;
    }
}
