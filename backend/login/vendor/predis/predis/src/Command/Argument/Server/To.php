<?php











namespace Predis\Command\Argument\Server;

use Predis\Command\Argument\ArrayableArgument;

class To implements ArrayableArgument
{
    private const KEYWORD = 'TO';
    private const FORCE_KEYWORD = 'FORCE';

    


    private $host;

    


    private $port;

    


    private $isForce;

    public function __construct(string $host, int $port, bool $isForce = false)
    {
        $this->host = $host;
        $this->port = $port;
        $this->isForce = $isForce;
    }

    


    public function toArray(): array
    {
        $arguments = [self::KEYWORD, $this->host, $this->port];

        if ($this->isForce) {
            $arguments[] = self::FORCE_KEYWORD;
        }

        return $arguments;
    }
}
