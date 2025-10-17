<?php











namespace Predis\Protocol\Parser;

use Predis\Protocol\Parser\Strategy\ParserStrategyInterface;

interface ParserStrategyResolverInterface
{
    





    public function resolve(int $protocolVersion): ParserStrategyInterface;
}
