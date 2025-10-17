<?php











namespace Predis\Protocol\Parser;

use Throwable;
use UnexpectedValueException;

class UnexpectedTypeException extends UnexpectedValueException
{
    


    protected $type;

    public function __construct(string $type, $message = '', $code = 0, ?Throwable $previous = null)
    {
        $this->type = $type;

        parent::__construct($message, $code, $previous);
    }

    


    public function getType(): string
    {
        return $this->type;
    }
}
