<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;




class XPENDING extends RedisCommand
{
    public function getId(): string
    {
        return 'XPENDING';
    }

    public function setArguments(array $arguments): void
    {
        if (count($arguments) < 2) {
            return;
        }

        $processedArguments = array_slice($arguments, 0, 2);
        $minIdleTime = $arguments[2] ?? null;
        $start = $arguments[3] ?? null;
        $end = $arguments[4] ?? null;
        $count = $arguments[5] ?? null;
        $consumer = $arguments[6] ?? null;

        if ($start !== null && $end !== null && $count !== null) {
            if ($minIdleTime !== null) {
                array_push($processedArguments, 'IDLE', $minIdleTime);
            }
            array_push($processedArguments, $start, $end, $count);
            if ($consumer !== null) {
                $processedArguments[] = $consumer;
            }
        }

        parent::setArguments($processedArguments);
    }

    public function parseResponse($data): array
    {
        if ($this->getArgument(2) !== null) {
            return $data;
        }

        [$pending, $minId, $maxId, $consumers] = $data;
        if (is_array($consumers)) {
            $parsedConsumers = [];
            foreach ($consumers as [$consumer, $num]) {
                $parsedConsumers[$consumer] = (int) $num;
            }
        } else {
            $parsedConsumers = $consumers;
        }

        return [$pending, $minId, $maxId, $parsedConsumers];
    }

    public function parseResp3Response($data): array
    {
        return $this->parseResponse($data);
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
