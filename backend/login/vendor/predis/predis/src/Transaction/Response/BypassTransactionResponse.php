<?php











namespace Predis\Transaction\Response;

use Predis\Response\ResponseInterface;




class BypassTransactionResponse implements ResponseInterface
{
    


    private $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    


    public function getResponse()
    {
        return $this->response;
    }
}
