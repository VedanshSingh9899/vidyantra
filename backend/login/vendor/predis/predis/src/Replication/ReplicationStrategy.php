<?php











namespace Predis\Replication;

use Predis\Command\CommandInterface;
use Predis\NotSupportedException;




class ReplicationStrategy
{
    protected $disallowed;
    protected $readonly;
    protected $readonlySHA1;
    protected $loadBalancing = true;

    public function __construct()
    {
        $this->disallowed = $this->getDisallowedOperations();
        $this->readonly = $this->getReadOnlyOperations();
        $this->readonlySHA1 = [];
    }

    








    public function isReadOperation(CommandInterface $command)
    {
        if (!$this->loadBalancing) {
            return false;
        }

        if (isset($this->disallowed[$id = $command->getId()])) {
            throw new NotSupportedException(
                "The command '$id' is not allowed in replication mode."
            );
        }

        if (isset($this->readonly[$id])) {
            if (true === $readonly = $this->readonly[$id]) {
                return true;
            }

            return call_user_func($readonly, $command);
        }

        if (($eval = $id === 'EVAL') || $id === 'EVALSHA') {
            $argument = $command->getArgument(0);
            $sha1 = $eval ? sha1(strval($argument)) : $argument;

            if (isset($this->readonlySHA1[$sha1])) {
                if (true === $readonly = $this->readonlySHA1[$sha1]) {
                    return true;
                }

                return call_user_func($readonly, $command);
            }
        }

        return false;
    }

    







    public function isDisallowedOperation(CommandInterface $command)
    {
        return isset($this->disallowed[$command->getId()]);
    }

    







    protected function isBitfieldReadOnly(CommandInterface $command)
    {
        $arguments = $command->getArguments();
        $argc = count($arguments);

        if ($argc >= 2) {
            for ($i = 1; $i < $argc; ++$i) {
                $argument = strtoupper($arguments[$i]);
                if ($argument === 'SET' || $argument === 'INCRBY') {
                    return false;
                }
            }
        }

        return true;
    }

    







    protected function isGeoradiusReadOnly(CommandInterface $command)
    {
        $arguments = $command->getArguments();
        $argc = count($arguments);
        $startIndex = $command->getId() === 'GEORADIUS' ? 5 : 4;

        if ($argc > $startIndex) {
            for ($i = $startIndex; $i < $argc; ++$i) {
                $argument = strtoupper($arguments[$i]);
                if ($argument === 'STORE' || $argument === 'STOREDIST') {
                    return false;
                }
            }
        }

        return true;
    }

    









    public function setCommandReadOnly($commandID, $readonly = true)
    {
        $commandID = strtoupper($commandID);

        if ($readonly) {
            $this->readonly[$commandID] = $readonly;
        } else {
            unset($this->readonly[$commandID]);
        }
    }

    









    public function setScriptReadOnly($script, $readonly = true)
    {
        $sha1 = sha1($script);

        if ($readonly) {
            $this->readonlySHA1[$sha1] = $readonly;
        } else {
            unset($this->readonlySHA1[$sha1]);
        }
    }

    




    protected function getDisallowedOperations()
    {
        return [
            'SHUTDOWN' => true,
            'INFO' => true,
            'DBSIZE' => true,
            'LASTSAVE' => true,
            'CONFIG' => true,
            'MONITOR' => true,
            'SLAVEOF' => true,
            'SAVE' => true,
            'BGSAVE' => true,
            'BGREWRITEAOF' => true,
            'SLOWLOG' => true,
        ];
    }

    




    protected function getReadOnlyOperations()
    {
        return [
            'EXISTS' => true,
            'TYPE' => true,
            'KEYS' => true,
            'SCAN' => true,
            'RANDOMKEY' => true,
            'TTL' => true,
            'GET' => true,
            'MGET' => true,
            'SUBSTR' => true,
            'STRLEN' => true,
            'GETRANGE' => true,
            'GETBIT' => true,
            'LLEN' => true,
            'LRANGE' => true,
            'LINDEX' => true,
            'SCARD' => true,
            'SISMEMBER' => true,
            'SINTER' => true,
            'SUNION' => true,
            'SDIFF' => true,
            'SMEMBERS' => true,
            'SSCAN' => true,
            'SRANDMEMBER' => true,
            'ZRANGE' => true,
            'ZREVRANGE' => true,
            'ZRANGEBYSCORE' => true,
            'ZREVRANGEBYSCORE' => true,
            'ZCARD' => true,
            'ZSCORE' => true,
            'ZCOUNT' => true,
            'ZRANK' => true,
            'ZREVRANK' => true,
            'ZSCAN' => true,
            'ZLEXCOUNT' => true,
            'ZRANGEBYLEX' => true,
            'ZREVRANGEBYLEX' => true,
            'HGET' => true,
            'HMGET' => true,
            'HEXISTS' => true,
            'HLEN' => true,
            'HKEYS' => true,
            'HVALS' => true,
            'HGETALL' => true,
            'HSCAN' => true,
            'HSTRLEN' => true,
            'PING' => true,
            'AUTH' => true,
            'SELECT' => true,
            'ECHO' => true,
            'QUIT' => true,
            'OBJECT' => true,
            'BITCOUNT' => true,
            'BITPOS' => true,
            'TIME' => true,
            'PFCOUNT' => true,
            'BITFIELD' => [$this, 'isBitfieldReadOnly'],
            'GEOHASH' => true,
            'GEOPOS' => true,
            'GEODIST' => true,
            'GEORADIUS' => [$this, 'isGeoradiusReadOnly'],
            'GEORADIUSBYMEMBER' => [$this, 'isGeoradiusReadOnly'],
            'GEOSEARCH' => true,
        ];
    }

    





    public function disableLoadBalancing(): self
    {
        $this->loadBalancing = false;

        return $this;
    }
}
