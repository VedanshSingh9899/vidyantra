<?php











namespace Predis\Command;

use Predis\ClientConfiguration;
use UnexpectedValueException;












final class RawCommand implements CommandInterface
{
    private $slot;
    private $commandID;
    private $arguments;

    



    public function __construct($commandID, array $arguments = [])
    {
        $this->commandID = strtoupper($commandID);
        $this->setArguments($arguments);
    }

    







    public static function create($commandID, ...$args)
    {
        $arguments = func_get_args();

        return new static(array_shift($arguments), $arguments);
    }

    


    public function getId()
    {
        return $this->commandID;
    }

    


    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
        unset($this->slot);
    }

    


    public function setRawArguments(array $arguments)
    {
        $this->setArguments($arguments);
    }

    


    public function getArguments()
    {
        return $this->arguments;
    }

    


    public function getArgument($index)
    {
        if (isset($this->arguments[$index])) {
            return $this->arguments[$index];
        }
    }

    


    public function setSlot($slot)
    {
        $this->slot = $slot;
    }

    


    public function getSlot()
    {
        return $this->slot ?? null;
    }

    


    public function parseResponse($data)
    {
        return $data;
    }

    


    public function parseResp3Response($data)
    {
        return $data;
    }

    


    public function serializeCommand(): string
    {
        $commandID = $this->getId();
        $arguments = $this->getArguments();

        $cmdlen = strlen($commandID);
        $reqlen = count($arguments) + 1;

        $buffer = "*{$reqlen}\r\n\${$cmdlen}\r\n{$commandID}\r\n";

        foreach ($arguments as $argument) {
            $arglen = strlen(strval($argument));
            $buffer .= "\${$arglen}\r\n{$argument}\r\n";
        }

        return $buffer;
    }

    public static function deserializeCommand(string $serializedCommand): CommandInterface
    {
        if ($serializedCommand[0] !== '*') {
            throw new UnexpectedValueException('Invalid serializing format');
        }

        $commandArray = explode("\r\n", $serializedCommand);
        $commandId = $commandArray[2];
        $classPath = __NAMESPACE__ . '\Redis\\';

        
        if (count($commandIdArray = explode('.', $commandId)) > 1) {
            
            $moduleConfiguration = array_filter(
                ClientConfiguration::getModules(),
                static function ($module) use ($commandIdArray) {
                    return $module['commandPrefix'] === $commandIdArray[0];
                }
            );

            $commandClass = strtoupper($commandIdArray[0] . $commandIdArray[1]);
            $classPath .= array_shift($moduleConfiguration)['name'] . '\\' . $commandClass;
        } else {
            $classPath .= $commandIdArray[0];
        }

        $command = new $classPath();
        $arguments = [];

        for ($i = 4, $iMax = count($commandArray); $i < $iMax; $i++) {
            $arguments[] = $commandArray[$i];
            ++$i;
        }

        $command->setArguments($arguments);

        return $command;
    }
}
