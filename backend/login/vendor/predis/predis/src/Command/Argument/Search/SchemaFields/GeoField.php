<?php











namespace Predis\Command\Argument\Search\SchemaFields;

class GeoField extends AbstractField
{
    






    public function __construct(
        string $identifier,
        string $alias = '',
        $sortable = self::NOT_SORTABLE,
        bool $noIndex = false,
        bool $allowsMissing = false
    ) {
        $this->setCommonOptions('GEO', $identifier, $alias, $sortable, $noIndex, $allowsMissing);
    }
}
