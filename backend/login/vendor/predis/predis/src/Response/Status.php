<?php











namespace Predis\Response;




class Status implements ResponseInterface
{
    private static $OK;
    private static $QUEUED;

    private $payload;

    


    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    




    public function __toString()
    {
        return $this->payload;
    }

    




    public function getPayload()
    {
        return $this->payload;
    }

    









    public static function get($payload)
    {
        switch ($payload) {
            case 'OK':
            case 'QUEUED':
                if (isset(self::$$payload)) {
                    return self::$$payload;
                }

                return self::$$payload = new self($payload);

            default:
                return new self($payload);
        }
    }
}
