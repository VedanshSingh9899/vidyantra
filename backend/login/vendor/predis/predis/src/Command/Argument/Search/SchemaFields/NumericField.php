<?php











namespace Predis\Command\Argument\Search\SchemaFields;

class NumericField extends AbstractField
{
    






    public function __construct(
        string $identifier,
        string $alias = '',
        $sortable = self::NOT_SORTABLE,
        bool $noIndex = false,
        bool $allowsMissing = false
    ) {
        $this->setCommonOptions('NUMERIC', $identifier, $alias, $sortable, $noIndex, $allowsMissing);
    }
}
