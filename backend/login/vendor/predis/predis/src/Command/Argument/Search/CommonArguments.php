<?php











namespace Predis\Command\Argument\Search;

use Predis\Command\Argument\ArrayableArgument;

class CommonArguments implements ArrayableArgument
{
    


    protected $arguments = [];

    





    public function language(string $defaultLanguage = 'english'): self
    {
        $this->arguments[] = 'LANGUAGE';
        $this->arguments[] = $defaultLanguage;

        return $this;
    }

    







    public function dialect(string $dialect): self
    {
        $this->arguments[] = 'DIALECT';
        $this->arguments[] = $dialect;

        return $this;
    }

    




    public function skipInitialScan(): self
    {
        $this->arguments[] = 'SKIPINITIALSCAN';

        return $this;
    }

    





    public function payload(string $payload): self
    {
        $this->arguments[] = 'PAYLOAD';
        $this->arguments[] = $payload;

        return $this;
    }

    




    public function withScores(): self
    {
        $this->arguments[] = 'WITHSCORES';

        return $this;
    }

    




    public function withPayloads(): self
    {
        $this->arguments[] = 'WITHPAYLOADS';

        return $this;
    }

    




    public function verbatim(): self
    {
        $this->arguments[] = 'VERBATIM';

        return $this;
    }

    





    public function timeout(int $timeout): self
    {
        $this->arguments[] = 'TIMEOUT';
        $this->arguments[] = $timeout;

        return $this;
    }

    






    public function limit(int $offset, int $num): self
    {
        array_push($this->arguments, 'LIMIT', $offset, $num);

        return $this;
    }

    





    public function filter(string $filter): self
    {
        $this->arguments[] = 'FILTER';
        $this->arguments[] = $filter;

        return $this;
    }

    







    public function params(array $nameValuesDictionary): self
    {
        $this->arguments[] = 'PARAMS';
        $this->arguments[] = count($nameValuesDictionary);
        $this->arguments = array_merge($this->arguments, $nameValuesDictionary);

        return $this;
    }

    


    public function toArray(): array
    {
        return $this->arguments;
    }
}
