<?php











namespace Predis\Command\Argument\Search\SchemaFields;

class TextField extends AbstractField
{
    











    public function __construct(
        string $identifier,
        string $alias = '',
        $sortable = self::NOT_SORTABLE,
        bool $noIndex = false,
        bool $noStem = false,
        string $phonetic = '',
        int $weight = 1,
        bool $withSuffixTrie = false,
        bool $allowsEmpty = false,
        bool $allowsMissing = false
    ) {
        $this->setCommonOptions('TEXT', $identifier, $alias, $sortable, $noIndex, $allowsMissing);

        if ($noStem) {
            $this->fieldArguments[] = 'NOSTEM';
        }

        if ($phonetic !== '') {
            $this->fieldArguments[] = 'PHONETIC';
            $this->fieldArguments[] = $phonetic;
        }

        if ($weight !== 1) {
            $this->fieldArguments[] = 'WEIGHT';
            $this->fieldArguments[] = $weight;
        }

        if ($withSuffixTrie) {
            $this->fieldArguments[] = 'WITHSUFFIXTRIE';
        }

        if ($allowsEmpty) {
            $this->fieldArguments[] = 'INDEXEMPTY';
        }
    }
}
