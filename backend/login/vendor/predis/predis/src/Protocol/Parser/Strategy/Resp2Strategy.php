<?php











namespace Predis\Protocol\Parser\Strategy;

use Predis\Protocol\Parser\UnexpectedTypeException;
use Predis\Response\Error;
use Predis\Response\ErrorInterface;
use Predis\Response\Status as StatusResponse;

class Resp2Strategy implements ParserStrategyInterface
{
    public const TYPE_ARRAY = 'array';
    public const TYPE_BULK_STRING = 'bulkString';

    




    protected $typeCallbacks = [
        '+' => 'parseSimpleString',
        '-' => 'parseError',
        ':' => 'parseInteger',
        '*' => 'parseArray',
        '$' => 'parseBulkString',
    ];

    




    protected $statusResponse = [
        'OK',
        'QUEUED',
        'NOKEY',
        'PONG',
    ];

    


    public function parseData(string $data)
    {
        $type = $data[0];
        $payload = substr($data, 1, -2);

        if (!array_key_exists($type, $this->typeCallbacks)) {
            throw new UnexpectedTypeException($type, 'Unexpected data type given.');
        }

        $callback = $this->typeCallbacks[$type];

        return $this->$callback($payload);
    }

    





    protected function parseSimpleString(string $string)
    {
        if (in_array($string, $this->statusResponse)) {
            return StatusResponse::get($string);
        }

        return $string;
    }

    





    protected function parseError(string $string): ErrorInterface
    {
        return new Error($string);
    }

    





    protected function parseInteger(string $string): int
    {
        return (int) $string;
    }

    





    protected function parseArray(string $string): ?array
    {
        $count = (int) $string;

        if ($count === -1) {
            return null;
        }

        return [
            'type' => self::TYPE_ARRAY,
            'value' => $count,
        ];
    }

    





    protected function parseBulkString(string $string): ?array
    {
        $size = (int) $string;

        if ($size === -1) {
            return null;
        }

        return [
            'type' => self::TYPE_BULK_STRING,
            'value' => $size,
        ];
    }
}
