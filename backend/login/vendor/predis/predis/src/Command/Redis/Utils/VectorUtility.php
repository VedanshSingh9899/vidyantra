<?php











namespace Predis\Command\Redis\Utils;

class VectorUtility
{
    






    public static function toBlob(array $vector, string $format = 'f*'): string
    {
        return pack($format, ...$vector);
    }

    






    public static function toArray(string $vector, string $format = 'f*'): array
    {
        return unpack($format, $vector);
    }
}
