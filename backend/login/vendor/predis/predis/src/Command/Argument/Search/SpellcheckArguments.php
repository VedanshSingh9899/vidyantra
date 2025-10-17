<?php











namespace Predis\Command\Argument\Search;

use InvalidArgumentException;

class SpellcheckArguments extends CommonArguments
{
    


    private $termsEnum = [
        'include' => 'INCLUDE',
        'exclude' => 'EXCLUDE',
    ];

    




    public function distance(int $distance): self
    {
        $this->arguments[] = 'DISTANCE';
        $this->arguments[] = $distance;

        return $this;
    }

    







    public function terms(string $dictionary, string $modifier = 'INCLUDE', string ...$terms): self
    {
        if (!in_array(strtoupper($modifier), $this->termsEnum)) {
            $enumValues = implode(', ', array_values($this->termsEnum));
            throw new InvalidArgumentException("Wrong modifier value given. Currently supports: {$enumValues}");
        }

        array_push($this->arguments, 'TERMS', $this->termsEnum[strtolower($modifier)], $dictionary, ...$terms);

        return $this;
    }
}
