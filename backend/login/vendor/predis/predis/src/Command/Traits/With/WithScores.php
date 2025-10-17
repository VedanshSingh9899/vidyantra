<?php











namespace Predis\Command\Traits\With;

use Predis\Command\Command;






trait WithScores
{
    public function setArguments(array $arguments)
    {
        $withScores = array_pop($arguments);

        if (is_bool($withScores) && $withScores) {
            $arguments[] = 'WITHSCORES';
        } elseif (!is_bool($withScores)) {
            $arguments[] = $withScores;
        }

        parent::setArguments($arguments);
    }

    




    private function isWithScoreModifier(): bool
    {
        $arguments = parent::getArguments();
        $lastArgument = (!empty($arguments)) ? $arguments[count($arguments) - 1] : null;

        return is_string($lastArgument) && strtoupper($lastArgument) === 'WITHSCORES';
    }

    public function parseResponse($data)
    {
        if ($this->isWithScoreModifier()) {
            $result = [];

            for ($i = 0, $iMax = count($data); $i < $iMax; ++$i) {
                if (is_array($data[$i])) {
                    $result[$data[$i][0]] = $data[$i][1]; 
                } elseif (array_key_exists($i + 1, $data)) {
                    $result[$data[$i]] = $data[++$i];
                }
            }

            return $result;
        }

        return $data;
    }
}
