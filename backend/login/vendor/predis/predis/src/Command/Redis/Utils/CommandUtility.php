<?php











namespace Predis\Command\Redis\Utils;

use UnexpectedValueException;

class CommandUtility
{
    







    public static function arrayToDictionary(array $array, ?callable $callback = null, bool $recursive = true): array
    {
        if (count($array) % 2 !== 0) {
            throw new UnexpectedValueException('Array must have an even number of arguments');
        }

        $dict = [];

        for ($i = 0; $i < count($array); $i += 2) {
            if (is_array($array[$i + 1])) {
                if ($recursive) {
                    $dict[$array[$i]] = self::arrayToDictionary($array[$i + 1], $callback, $recursive);
                } else {
                    $dict[$array[$i]] = $array[$i + 1];
                }
            } else {
                if ($callback) {
                    [$key, $value] = $callback($array[$i], $array[$i + 1]);
                } else {
                    $key = $array[$i];
                    $value = $array[$i + 1];
                }

                $dict[$key] = $value;
            }
        }

        return $dict;
    }
}
