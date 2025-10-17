<?php











namespace Predis\Command\Argument\Search;

class AggregateArguments extends CommonArguments
{
    


    private $sortingEnum = [
        'asc' => 'ASC',
        'desc' => 'DESC',
    ];

    





    public function load(string ...$fields): self
    {
        $arguments = func_get_args();

        $this->arguments[] = 'LOAD';

        if ($arguments[0] === '*') {
            $this->arguments[] = '*';

            return $this;
        }

        $this->arguments[] = count($arguments);
        $this->arguments = array_merge($this->arguments, $arguments);

        return $this;
    }

    





    public function groupBy(string ...$properties): self
    {
        $arguments = func_get_args();

        array_push($this->arguments, 'GROUPBY', count($arguments));
        $this->arguments = array_merge($this->arguments, $arguments);

        return $this;
    }

    











    public function reduce(string $function, ...$argument): self
    {
        $arguments = func_get_args();
        $functionValue = array_shift($arguments);
        $argumentsCounter = 0;

        for ($i = 0, $iMax = count($arguments); $i < $iMax; $i++) {
            if (true === $arguments[$i]) {
                $arguments[$i] = 'AS';
                $i++;
                continue;
            }

            $argumentsCounter++;
        }

        array_push($this->arguments, 'REDUCE', $functionValue);
        $this->arguments = array_merge($this->arguments, [$argumentsCounter], $arguments);

        return $this;
    }

    






    public function sortBy(int $max = 0, ...$properties): self
    {
        $arguments = func_get_args();
        $maxValue = array_shift($arguments);

        $this->arguments[] = 'SORTBY';
        $this->arguments = array_merge($this->arguments, [count($arguments)], $arguments);

        if ($maxValue !== 0) {
            array_push($this->arguments, 'MAX', $maxValue);
        }

        return $this;
    }

    







    public function apply(string $expression, string $as = ''): self
    {
        array_push($this->arguments, 'APPLY', $expression);

        if ($as !== '') {
            array_push($this->arguments, 'AS', $as);
        }

        return $this;
    }

    






    public function withCursor(int $readSize = 0, int $idleTime = 0): self
    {
        $this->arguments[] = 'WITHCURSOR';

        if ($readSize !== 0) {
            array_push($this->arguments, 'COUNT', $readSize);
        }

        if ($idleTime !== 0) {
            array_push($this->arguments, 'MAXIDLE', $idleTime);
        }

        return $this;
    }
}
