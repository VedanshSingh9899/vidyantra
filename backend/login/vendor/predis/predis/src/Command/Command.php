<?php











namespace Predis\Command;

use Predis\ClientConfiguration;
use UnexpectedValueException;




abstract class Command implements CommandInterface
{
    private $slot;
    private $arguments = [];

    


    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
        unset($this->slot);
    }

    


    public function setRawArguments(array $arguments)
    {
        $this->arguments = $arguments;
        unset($this->slot);
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

    






    public static function normalizeArguments(array $arguments)
    {
        if (count($arguments) === 1 && isset($arguments[0]) && is_array($arguments[0])) {
            return $arguments[0];
        }

        return $arguments;
    }

    






    public static function normalizeVariadic(array $arguments)
    {
        if (count($arguments) === 2 && is_array($arguments[1])) {
            return array_merge([$arguments[0]], $arguments[1]);
        }

        return $arguments;
    }

    




    public function filterArguments(): void
    {
        $this->arguments = array_filter($this->arguments, static function ($argument) {
            return $argument !== false && $argument !== null;
        });
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
