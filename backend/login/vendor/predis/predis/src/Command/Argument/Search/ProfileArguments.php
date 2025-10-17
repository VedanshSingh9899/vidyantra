<?php











namespace Predis\Command\Argument\Search;

use Predis\Command\Argument\ArrayableArgument;

class ProfileArguments implements ArrayableArgument
{
    


    protected $arguments = [];

    




    public function search(): self
    {
        $this->arguments[] = 'SEARCH';

        return $this;
    }

    




    public function aggregate(): self
    {
        $this->arguments[] = 'AGGREGATE';

        return $this;
    }

    




    public function limited(): self
    {
        $this->arguments[] = 'LIMITED';

        return $this;
    }

    





    public function query(string $query): self
    {
        $this->arguments[] = 'QUERY';
        $this->arguments[] = $query;

        return $this;
    }

    


    public function toArray(): array
    {
        return $this->arguments;
    }
}
