<?php











namespace Predis\Command\Argument\Search\SchemaFields;

abstract class AbstractField implements FieldInterface
{
    public const SORTABLE = true;
    public const NOT_SORTABLE = false;
    public const SORTABLE_UNF = 'UNF';

    


    protected $fieldArguments = [];

    








    protected function setCommonOptions(
        string $fieldType,
        string $identifier,
        string $alias = '',
        $sortable = self::NOT_SORTABLE,
        bool $noIndex = false,
        bool $allowsMissing = false
    ): void {
        $this->fieldArguments[] = $identifier;

        if ($alias !== '') {
            $this->fieldArguments[] = 'AS';
            $this->fieldArguments[] = $alias;
        }

        $this->fieldArguments[] = $fieldType;

        if ($sortable === self::SORTABLE) {
            $this->fieldArguments[] = 'SORTABLE';
        } elseif ($sortable === self::SORTABLE_UNF) {
            $this->fieldArguments[] = 'SORTABLE';
            $this->fieldArguments[] = 'UNF';
        }

        if ($noIndex) {
            $this->fieldArguments[] = 'NOINDEX';
        }

        if ($allowsMissing) {
            $this->fieldArguments[] = 'INDEXMISSING';
        }
    }

    


    public function toArray(): array
    {
        return $this->fieldArguments;
    }
}
