<?php











namespace Predis\Command\Processor;

use InvalidArgumentException;
use Predis\Command\CommandInterface;
use Predis\Command\PrefixableCommandInterface;





class KeyPrefixProcessor implements ProcessorInterface
{
    private $prefix;
    private $commands;

    


    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    




    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    




    public function getPrefix()
    {
        return $this->prefix;
    }

    


    public function process(CommandInterface $command)
    {
        if ($command instanceof PrefixableCommandInterface) {
            $command->prefixKeys($this->prefix);
        } elseif (isset($this->commands[$commandID = strtoupper($command->getId())])) {
            $this->commands[$commandID]($command, $this->prefix);
        }
    }

    















    public function setCommandHandler($commandID, $callback = null)
    {
        $commandID = strtoupper($commandID);

        if (!isset($callback)) {
            unset($this->commands[$commandID]);

            return;
        }

        if (!is_callable($callback)) {
            throw new InvalidArgumentException(
                'Callback must be a valid callable object or NULL'
            );
        }

        $this->commands[$commandID] = $callback;
    }

    


    public function __toString()
    {
        return $this->getPrefix();
    }
}
