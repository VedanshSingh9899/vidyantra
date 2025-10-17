<?php











namespace Predis\Command\Argument\Search\SchemaFields;

class TagField extends AbstractField
{
    








    public function __construct(
        string $identifier,
        string $alias = '',
        $sortable = self::NOT_SORTABLE,
        bool $noIndex = false,
        string $separator = ',',
        bool $caseSensitive = false,
        bool $allowsEmpty = false,
        bool $allowsMissing = false
    ) {
        $this->setCommonOptions('TAG', $identifier, $alias, $sortable, $noIndex, $allowsMissing);

        if ($separator !== ',') {
            $this->fieldArguments[] = 'SEPARATOR';
            $this->fieldArguments[] = $separator;
        }

        if ($caseSensitive) {
            $this->fieldArguments[] = 'CASESENSITIVE';
        }

        if ($allowsEmpty) {
            $this->fieldArguments[] = 'INDEXEMPTY';
        }
    }
}
