<?php











namespace Predis\Command\Argument\Search\SchemaFields;

class VectorField extends AbstractField
{
    


    protected $fieldArguments = [];

    





    public function __construct(
        string $fieldName,
        string $algorithm,
        array $attributeNameValueDictionary,
        string $alias = ''
    ) {
        $this->setCommonOptions('VECTOR', $fieldName, $alias);

        array_push($this->fieldArguments, $algorithm, count($attributeNameValueDictionary));
        $this->fieldArguments = array_merge($this->fieldArguments, $attributeNameValueDictionary);
    }

    


    public function toArray(): array
    {
        return $this->fieldArguments;
    }
}
