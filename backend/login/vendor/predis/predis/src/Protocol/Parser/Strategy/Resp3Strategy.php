<?php











namespace Predis\Protocol\Parser\Strategy;

class Resp3Strategy extends Resp2Strategy
{
    public const TYPE_BLOB_ERROR = 'blobError';
    public const TYPE_VERBATIM_STRING = 'verbatimString';
    public const TYPE_MAP = 'map';
    public const TYPE_SET = 'set';
    public const TYPE_PUSH = 'push';

    


    public const VERBATIM_STRING_EXTENSION_OFFSET = 4;

    


    protected $resp3TypeCallbacks = [
        '_' => 'parseNull',
        ',' => 'parseDouble',
        '#' => 'parseBoolean',
        '!' => 'parseBlobError',
        '=' => 'parseVerbatimString',
        '(' => 'parseBigNumber',
        '%' => 'parseMap',
        '~' => 'parseSet',
        '>' => 'parsePush',
    ];

    public function __construct()
    {
        $this->typeCallbacks += $this->resp3TypeCallbacks;
    }

    




    protected function parseNull(string $string)
    {
        return null;
    }

    





    protected function parseDouble(string $string): float
    {
        if ($string === 'inf' || $string === '-inf') {
            return INF;
        }

        return (float) $string;
    }

    





    protected function parseBoolean(string $string): bool
    {
        return $string === 't';
    }

    





    protected function parseBlobError(string $string): array
    {
        return [
            'type' => self::TYPE_BLOB_ERROR,
            'value' => (int) $string,
        ];
    }

    





    protected function parseVerbatimString(string $string): array
    {
        return [
            'type' => self::TYPE_VERBATIM_STRING,
            'value' => (int) $string,
            'offset' => self::VERBATIM_STRING_EXTENSION_OFFSET,
        ];
    }

    






    protected function parseBigNumber(string $string)
    {
        if (bccomp($string, PHP_INT_MAX) === 1) {
            return (float) $string;
        }

        return $this->parseInteger($string);
    }

    





    protected function parseMap(string $string): array
    {
        return [
            'type' => self::TYPE_MAP,
            'value' => (int) $string,
        ];
    }

    





    protected function parseSet(string $string): array
    {
        return [
            'type' => self::TYPE_SET,
            'value' => (int) $string,
        ];
    }

    





    protected function parsePush(string $string): array
    {
        return [
            'type' => self::TYPE_PUSH,
            'value' => (int) $string,
        ];
    }
}
