<?php











namespace Predis\Command\Argument\Search;

use InvalidArgumentException;

class CreateArguments extends CommonArguments
{
    


    private $supportedDataTypesEnum = [
        'hash' => 'HASH',
        'json' => 'JSON',
    ];

    





    public function on(string $modifier = 'HASH'): self
    {
        if (in_array(strtoupper($modifier), $this->supportedDataTypesEnum)) {
            $this->arguments[] = 'ON';
            $this->arguments[] = $this->supportedDataTypesEnum[strtolower($modifier)];

            return $this;
        }

        $enumValues = implode(', ', array_values($this->supportedDataTypesEnum));
        throw new InvalidArgumentException("Wrong modifier value given. Currently supports: {$enumValues}");
    }

    





    public function prefix(array $prefixes): self
    {
        $this->arguments[] = 'PREFIX';
        $this->arguments[] = count($prefixes);
        $this->arguments = array_merge($this->arguments, $prefixes);

        return $this;
    }

    





    public function languageField(string $languageAttribute): self
    {
        $this->arguments[] = 'LANGUAGE_FIELD';
        $this->arguments[] = $languageAttribute;

        return $this;
    }

    





    public function score(float $defaultScore = 1.0): self
    {
        $this->arguments[] = 'SCORE';
        $this->arguments[] = $defaultScore;

        return $this;
    }

    





    public function scoreField(string $scoreAttribute): self
    {
        $this->arguments[] = 'SCORE_FIELD';
        $this->arguments[] = $scoreAttribute;

        return $this;
    }

    




    public function maxTextFields(): self
    {
        $this->arguments[] = 'MAXTEXTFIELDS';

        return $this;
    }

    




    public function noOffsets(): self
    {
        $this->arguments[] = 'NOOFFSETS';

        return $this;
    }

    





    public function temporary(int $seconds): self
    {
        $this->arguments[] = 'TEMPORARY';
        $this->arguments[] = $seconds;

        return $this;
    }

    




    public function noHl(): self
    {
        $this->arguments[] = 'NOHL';

        return $this;
    }

    




    public function noFields(): self
    {
        $this->arguments[] = 'NOFIELDS';

        return $this;
    }

    




    public function noFreqs(): self
    {
        $this->arguments[] = 'NOFREQS';

        return $this;
    }

    





    public function stopWords(array $stopWords): self
    {
        $this->arguments[] = 'STOPWORDS';
        $this->arguments[] = count($stopWords);
        $this->arguments = array_merge($this->arguments, $stopWords);

        return $this;
    }
}
