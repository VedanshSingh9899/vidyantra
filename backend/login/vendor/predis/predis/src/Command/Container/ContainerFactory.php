<?php











namespace Predis\Command\Container;

use Predis\ClientConfiguration;
use Predis\ClientInterface;
use UnexpectedValueException;

class ContainerFactory
{
    private const CONTAINER_NAMESPACE = "Predis\Command\Container";

    




    private static $specialMappings = [
        'FUNCTION' => FUNCTIONS::class,
    ];

    






    public static function create(ClientInterface $client, string $containerCommandID): ContainerInterface
    {
        $containerCommandID = strtoupper($containerCommandID);
        $commandModule = self::resolveCommandModuleByPrefix($containerCommandID);

        if (null !== $commandModule) {
            if (class_exists($containerClass = self::CONTAINER_NAMESPACE . '\\' . $commandModule . '\\' . $containerCommandID)) {
                return new $containerClass($client);
            }

            throw new UnexpectedValueException('Given module container command is not supported.');
        }

        if (class_exists($containerClass = self::CONTAINER_NAMESPACE . '\\' . $containerCommandID)) {
            return new $containerClass($client);
        }

        if (array_key_exists($containerCommandID, self::$specialMappings)) {
            $containerClass = self::$specialMappings[$containerCommandID];

            return new $containerClass($client);
        }

        throw new UnexpectedValueException('Given container command is not supported.');
    }

    



    private static function resolveCommandModuleByPrefix(string $commandID): ?string
    {
        $modules = ClientConfiguration::getModules();

        foreach ($modules as $module) {
            if (preg_match("/^{$module['commandPrefix']}/", $commandID)) {
                return $module['name'];
            }
        }

        return null;
    }
}
