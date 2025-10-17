<?php











namespace Predis\Command;

use Predis\ClientConfiguration;
use Predis\Command\Redis\FUNCTIONS;










class RedisFactory extends Factory
{
    private const COMMANDS_NAMESPACE = "Predis\Command\Redis";

    public function __construct()
    {
        $this->commands = [
            'ECHO' => 'Predis\Command\Redis\ECHO_',
            'EVAL' => 'Predis\Command\Redis\EVAL_',
            'OBJECT' => 'Predis\Command\Redis\OBJECT_',
            
            'FUNCTION' => FUNCTIONS::class,
        ];
    }

    


    public function getCommandClass(string $commandID): ?string
    {
        $commandID = strtoupper($commandID);

        if (isset($this->commands[$commandID]) || array_key_exists($commandID, $this->commands)) {
            return $this->commands[$commandID];
        }

        $commandClass = $this->resolve($commandID);

        if (null === $commandClass) {
            return null;
        }

        $this->commands[$commandID] = $commandClass;

        return $commandClass;
    }

    


    public function undefine(string $commandID): void
    {
        
        
        
        
        
        $this->commands[strtoupper($commandID)] = null;
    }

    





    private function resolve(string $commandID): ?string
    {
        if (class_exists($commandClass = self::COMMANDS_NAMESPACE . '\\' . $commandID)) {
            return $commandClass;
        }

        $commandModule = $this->resolveCommandModuleByPrefix($commandID);

        if (null === $commandModule) {
            return null;
        }

        if (class_exists($commandClass = self::COMMANDS_NAMESPACE . '\\' . $commandModule . '\\' . $commandID)) {
            return $commandClass;
        }

        return null;
    }

    private function resolveCommandModuleByPrefix(string $commandID): ?string
    {
        foreach (ClientConfiguration::getModules() as $module) {
            if (preg_match("/^{$module['commandPrefix']}/", $commandID)) {
                return $module['name'];
            }
        }

        return null;
    }
}
