<?php











namespace Predis\Protocol\Parser;

use InvalidArgumentException;
use Predis\Protocol\Parser\Strategy\ParserStrategyInterface;
use Predis\Protocol\Parser\Strategy\Resp2Strategy;
use Predis\Protocol\Parser\Strategy\Resp3Strategy;

class ParserStrategyResolver implements ParserStrategyResolverInterface
{
    


    protected $protocolStrategyMapping = [
        2 => Resp2Strategy::class,
        3 => Resp3Strategy::class,
    ];

    


    public function resolve(int $protocolVersion): ParserStrategyInterface
    {
        if (!array_key_exists($protocolVersion, $this->protocolStrategyMapping)) {
            throw new InvalidArgumentException('Invalid protocol version given.');
        }

        $strategy = $this->protocolStrategyMapping[$protocolVersion];

        return new $strategy();
    }
}
