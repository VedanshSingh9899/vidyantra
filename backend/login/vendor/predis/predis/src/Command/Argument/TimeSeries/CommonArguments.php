<?php











namespace Predis\Command\Argument\TimeSeries;

use Predis\Command\Argument\ArrayableArgument;
use UnexpectedValueException;

class CommonArguments implements ArrayableArgument
{
    public const POLICY_BLOCK = 'BLOCK';
    public const POLICY_FIRST = 'FIRST';
    public const POLICY_LAST = 'LAST';
    public const POLICY_MIN = 'MIN';
    public const POLICY_MAX = 'MAX';
    public const POLICY_SUM = 'SUM';

    public const ENCODING_UNCOMPRESSED = 'UNCOMPRESSED';
    public const ENCODING_COMPRESSED = 'COMPRESSED';

    


    protected $arguments = [];

    





    public function retentionMsecs(int $retentionPeriod): self
    {
        array_push($this->arguments, 'RETENTION', $retentionPeriod);

        return $this;
    }

    






    public function ignore(int $maxTimeDiff, float $maxValDiff): self
    {
        if ($maxTimeDiff < 0 || $maxValDiff < 0) {
            throw new UnexpectedValueException('Ignore does not accept negative values');
        }

        array_push($this->arguments, 'IGNORE', $maxTimeDiff, $maxValDiff);

        return $this;
    }

    





    public function chunkSize(int $size): self
    {
        array_push($this->arguments, 'CHUNK_SIZE', $size);

        return $this;
    }

    





    public function duplicatePolicy(string $policy = self::POLICY_BLOCK): self
    {
        array_push($this->arguments, 'DUPLICATE_POLICY', $policy);

        return $this;
    }

    





    public function labels(...$labelValuePair): self
    {
        array_push($this->arguments, 'LABELS', ...$labelValuePair);

        return $this;
    }

    





    public function encoding(string $encoding = self::ENCODING_COMPRESSED): self
    {
        array_push($this->arguments, 'ENCODING', $encoding);

        return $this;
    }

    





    public function latest(): self
    {
        $this->arguments[] = 'LATEST';

        return $this;
    }

    




    public function withLabels(): self
    {
        $this->arguments[] = 'WITHLABELS';

        return $this;
    }

    




    public function selectedLabels(string ...$labels): self
    {
        array_push($this->arguments, 'SELECTED_LABELS', ...$labels);

        return $this;
    }

    


    public function toArray(): array
    {
        return $this->arguments;
    }
}
