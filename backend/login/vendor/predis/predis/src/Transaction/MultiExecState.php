<?php











namespace Predis\Transaction;




class MultiExecState
{
    public const INITIALIZED = 1;    
    public const INSIDEBLOCK = 2;    
    public const DISCARDED = 4;    
    public const CAS = 8;    
    public const WATCH = 16;   

    private $flags;

    public function __construct()
    {
        $this->flags = 0;
    }

    




    public function set($flags)
    {
        $this->flags = $flags;
    }

    




    public function get()
    {
        return $this->flags;
    }

    




    public function flag($flags)
    {
        $this->flags |= $flags;
    }

    




    public function unflag($flags)
    {
        $this->flags &= ~$flags;
    }

    






    public function check($flags)
    {
        return ($this->flags & $flags) === $flags;
    }

    


    public function reset()
    {
        $this->flags = 0;
    }

    




    public function isReset()
    {
        return $this->flags === 0;
    }

    




    public function isInitialized()
    {
        return $this->check(self::INITIALIZED);
    }

    




    public function isExecuting()
    {
        return $this->check(self::INSIDEBLOCK);
    }

    




    public function isCAS()
    {
        return $this->check(self::CAS);
    }

    




    public function isWatchAllowed()
    {
        return $this->check(self::INITIALIZED) && !$this->check(self::CAS);
    }

    




    public function isWatching()
    {
        return $this->check(self::WATCH);
    }

    




    public function isDiscarded()
    {
        return $this->check(self::DISCARDED);
    }
}
