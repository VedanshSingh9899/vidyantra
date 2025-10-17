<?php











namespace Predis\Command\Argument\Search;

use InvalidArgumentException;

class SearchArguments extends CommonArguments
{
    


    private $sortingEnum = [
        'asc' => 'ASC',
        'desc' => 'DESC',
    ];

    




    public function noContent(): self
    {
        $this->arguments[] = 'NOCONTENT';

        return $this;
    }

    




    public function withSortKeys(): self
    {
        $this->arguments[] = 'WITHSORTKEYS';

        return $this;
    }

    








    public function searchFilter(array ...$filter): self
    {
        $arguments = func_get_args();

        foreach ($arguments as $argument) {
            array_push($this->arguments, 'FILTER', ...$argument);
        }

        return $this;
    }

    





    public function geoFilter(array ...$filter): self
    {
        $arguments = func_get_args();

        foreach ($arguments as $argument) {
            array_push($this->arguments, 'GEOFILTER', ...$argument);
        }

        return $this;
    }

    





    public function inKeys(array $keys): self
    {
        $this->arguments[] = 'INKEYS';
        $this->arguments[] = count($keys);
        $this->arguments = array_merge($this->arguments, $keys);

        return $this;
    }

    





    public function inFields(array $fields): self
    {
        $this->arguments[] = 'INFIELDS';
        $this->arguments[] = count($fields);
        $this->arguments = array_merge($this->arguments, $fields);

        return $this;
    }

    















    public function addReturn(int $count, ...$identifier): self
    {
        $arguments = func_get_args();

        $this->arguments[] = 'RETURN';

        for ($i = 1, $iMax = count($arguments); $i < $iMax; $i++) {
            if (true === $arguments[$i]) {
                $arguments[$i] = 'AS';
            }
        }

        $this->arguments = array_merge($this->arguments, $arguments);

        return $this;
    }

    








    public function summarize(array $fields = [], int $frags = 0, int $len = 0, string $separator = ''): self
    {
        $this->arguments[] = 'SUMMARIZE';

        if (!empty($fields)) {
            $this->arguments[] = 'FIELDS';
            $this->arguments[] = count($fields);
            $this->arguments = array_merge($this->arguments, $fields);
        }

        if ($frags !== 0) {
            $this->arguments[] = 'FRAGS';
            $this->arguments[] = $frags;
        }

        if ($len !== 0) {
            $this->arguments[] = 'LEN';
            $this->arguments[] = $len;
        }

        if ($separator !== '') {
            $this->arguments[] = 'SEPARATOR';
            $this->arguments[] = $separator;
        }

        return $this;
    }

    







    public function highlight(array $fields = [], string $openTag = '', string $closeTag = ''): self
    {
        $this->arguments[] = 'HIGHLIGHT';

        if (!empty($fields)) {
            $this->arguments[] = 'FIELDS';
            $this->arguments[] = count($fields);
            $this->arguments = array_merge($this->arguments, $fields);
        }

        if ($openTag !== '' && $closeTag !== '') {
            array_push($this->arguments, 'TAGS', $openTag, $closeTag);
        }

        return $this;
    }

    






    public function slop(int $slop): self
    {
        $this->arguments[] = 'SLOP';
        $this->arguments[] = $slop;

        return $this;
    }

    





    public function inOrder(): self
    {
        $this->arguments[] = 'INORDER';

        return $this;
    }

    





    public function expander(string $expander): self
    {
        $this->arguments[] = 'EXPANDER';
        $this->arguments[] = $expander;

        return $this;
    }

    





    public function scorer(string $scorer): self
    {
        $this->arguments[] = 'SCORER';
        $this->arguments[] = $scorer;

        return $this;
    }

    





    public function explainScore(): self
    {
        $this->arguments[] = 'EXPLAINSCORE';

        return $this;
    }

    









    public function sortBy(string $sortAttribute, string $orderBy = 'asc'): self
    {
        $this->arguments[] = 'SORTBY';
        $this->arguments[] = $sortAttribute;

        if (in_array(strtoupper($orderBy), $this->sortingEnum)) {
            $this->arguments[] = $this->sortingEnum[strtolower($orderBy)];
        } else {
            $enumValues = implode(', ', array_values($this->sortingEnum));
            throw new InvalidArgumentException("Wrong order direction value given. Currently supports: {$enumValues}");
        }

        return $this;
    }
}
