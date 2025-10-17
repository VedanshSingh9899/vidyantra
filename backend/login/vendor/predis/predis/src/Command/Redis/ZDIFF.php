<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;
use Predis\Command\Traits\Keys;
use Predis\Command\Traits\With\WithScores;







class ZDIFF extends RedisCommand
{
    use WithScores {
        WithScores::setArguments as setWithScore;
    }
    use Keys {
        Keys::setArguments as setKeys;
    }

    protected static $keysArgumentPositionOffset = 0;

    public function getId()
    {
        return 'ZDIFF';
    }

    public function setArguments(array $arguments)
    {
        $this->setKeys($arguments);
        $arguments = $this->getArguments();

        $this->setWithScore($arguments);
    }

    



    public function parseResp3Response($data)
    {
        $parsedData = [];

        foreach ($data as $element) {
            $parsedData[] = $this->parseResponse($element);
        }

        return $parsedData;
    }
}
