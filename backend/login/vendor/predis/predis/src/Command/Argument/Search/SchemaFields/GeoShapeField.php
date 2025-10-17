<?php











namespace Predis\Command\Argument\Search\SchemaFields;

class GeoShapeField extends AbstractField
{
    public const COORD_FLAT = 'FLAT';

    






    public function __construct(
        string $identifier,
        string $alias = '',
        $sortable = self::NOT_SORTABLE,
        bool $noIndex = false,
        ?string $coordSystem = null
    ) {
        $this->fieldArguments[] = $identifier;

        if ($alias !== '') {
            $this->fieldArguments[] = 'AS';
            $this->fieldArguments[] = $alias;
        }

        $this->fieldArguments[] = 'GEOSHAPE';

        if (null !== $coordSystem) {
            $this->fieldArguments[] = $coordSystem;
        }

        if ($sortable === self::SORTABLE) {
            $this->fieldArguments[] = 'SORTABLE';
        } elseif ($sortable === self::SORTABLE_UNF) {
            $this->fieldArguments[] = 'SORTABLE';
            $this->fieldArguments[] = 'UNF';
        }

        if ($noIndex) {
            $this->fieldArguments[] = 'NOINDEX';
        }
    }
}
