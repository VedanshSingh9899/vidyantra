<?php











namespace Predis\Command\Redis\TopK;

use Predis\Command\PrefixableCommand as RedisCommand;






class TOPKLIST extends RedisCommand
{
    public function getId()
    {
        return 'TOPK.LIST';
    }

    public function setArguments(array $arguments)
    {
        if (!empty($arguments[1])) {
            $arguments[1] = 'WITHCOUNT';
        }

        parent::setArguments($arguments);
        $this->filterArguments();
    }

    public function parseResponse($data)
    {
        if ($this->isWithCountModifier()) {
            $result = [];

            for ($i = 0, $iMax = count($data); $i < $iMax; ++$i) {
                if (array_key_exists($i + 1, $data)) {
                    $result[(string) $data[$i]] = $data[++$i];
                }
            }

            return $result;
        }

        return $data;
    }

    



    public function parseResp3Response($data)
    {
        return $this->parseResponse($data);
    }

    




    private function isWithCountModifier(): bool
    {
        $arguments = $this->getArguments();
        $lastArgument = (!empty($arguments)) ? $arguments[count($arguments) - 1] : null;

        return is_string($lastArgument) && strtoupper($lastArgument) === 'WITHCOUNT';
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
