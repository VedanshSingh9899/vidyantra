<?php











namespace Predis;

class ClientConfiguration
{
    


    private static $config = [
        'modules' => [
            ['name' => 'Json', 'commandPrefix' => 'JSON'],
            ['name' => 'BloomFilter', 'commandPrefix' => 'BF'],
            ['name' => 'CuckooFilter', 'commandPrefix' => 'CF'],
            ['name' => 'CountMinSketch', 'commandPrefix' => 'CMS'],
            ['name' => 'TDigest', 'commandPrefix' => 'TDIGEST'],
            ['name' => 'TopK', 'commandPrefix' => 'TOPK'],
            ['name' => 'Search', 'commandPrefix' => 'FT'],
            ['name' => 'TimeSeries', 'commandPrefix' => 'TS'],
        ],
    ];

    




    public static function getModules(): array
    {
        return self::$config['modules'];
    }
}
