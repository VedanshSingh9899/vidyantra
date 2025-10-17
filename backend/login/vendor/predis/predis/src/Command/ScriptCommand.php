<?php











namespace Predis\Command;







abstract class ScriptCommand extends Command
{
    


    public function getId()
    {
        return 'EVALSHA';
    }

    




    abstract public function getScript();

    




    public function getScriptHash()
    {
        return sha1($this->getScript());
    }

    








    protected function getKeysCount()
    {
        return 0;
    }

    




    public function getKeys()
    {
        return array_slice($this->getArguments(), 2, $this->getKeysCount());
    }

    


    public function setArguments(array $arguments)
    {
        if (($numkeys = $this->getKeysCount()) && $numkeys < 0) {
            $numkeys = count($arguments) + $numkeys;
        }

        $arguments = array_merge([$this->getScriptHash(), (int) $numkeys], $arguments);

        parent::setArguments($arguments);
    }

    




    public function getEvalArguments()
    {
        $arguments = $this->getArguments();
        $arguments[0] = $this->getScript();

        return $arguments;
    }

    




    public function getEvalCommand()
    {
        return new RawCommand('EVAL', $this->getEvalArguments());
    }
}
