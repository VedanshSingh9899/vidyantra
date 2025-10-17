<?php











namespace Predis\Connection\Resource;

use Predis\Connection\ParametersInterface;
use Psr\Http\Message\StreamInterface;

interface StreamFactoryInterface
{
    





    public function createStream(ParametersInterface $parameters): StreamInterface;
}
