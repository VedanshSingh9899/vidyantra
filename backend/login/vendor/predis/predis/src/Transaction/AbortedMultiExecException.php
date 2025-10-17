<?php











namespace Predis\Transaction;

use Predis\PredisException;




class AbortedMultiExecException extends PredisException
{
    private $transaction;

    




    public function __construct(MultiExec $transaction, $message, $code = 0)
    {
        parent::__construct($message, is_null($code) ? 0 : $code);

        $this->transaction = $transaction;
    }

    




    public function getTransaction()
    {
        return $this->transaction;
    }
}
