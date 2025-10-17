<?php











namespace Predis\Session;

use Predis\ClientInterface;
use ReturnTypeWillChange;
use SessionHandlerInterface;









class Handler implements SessionHandlerInterface
{
    protected $client;
    protected $ttl;

    



    public function __construct(ClientInterface $client, array $options = [])
    {
        $this->client = $client;

        if (isset($options['gc_maxlifetime'])) {
            $this->ttl = (int) $options['gc_maxlifetime'];
        } else {
            $this->ttl = ini_get('session.gc_maxlifetime');
        }
    }

    


    public function register()
    {
        session_set_save_handler($this, true);
    }

    




    #[ReturnTypeWillChange]
    public function open($save_path, $session_id)
    {
        
        return true;
    }

    


    #[ReturnTypeWillChange]
    public function close()
    {
        
        return true;
    }

    



    #[ReturnTypeWillChange]
    public function gc($maxlifetime)
    {
        
        return true;
    }

    



    #[ReturnTypeWillChange]
    public function read($session_id)
    {
        if ($data = $this->client->get($session_id)) {
            return $data;
        }

        return '';
    }

    




    #[ReturnTypeWillChange]
    public function write($session_id, $session_data)
    {
        $this->client->setex($session_id, $this->ttl, $session_data);

        return true;
    }

    



    #[ReturnTypeWillChange]
    public function destroy($session_id)
    {
        $this->client->del($session_id);

        return true;
    }

    




    public function getClient()
    {
        return $this->client;
    }

    




    public function getMaxLifeTime()
    {
        return $this->ttl;
    }
}
