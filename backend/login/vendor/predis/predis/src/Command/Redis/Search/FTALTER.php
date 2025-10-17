<?php











namespace Predis\Command\Redis\Search;

use Predis\Command\Argument\Search\SchemaFields\FieldInterface;
use Predis\Command\PrefixableCommand as RedisCommand;

class FTALTER extends RedisCommand
{
    public function getId()
    {
        return 'FT.ALTER';
    }

    public function setArguments(array $arguments)
    {
        [$index, $schema] = $arguments;
        $commandArguments = (!empty($arguments[2])) ? $arguments[2]->toArray() : [];

        $schema = array_reduce($schema, static function (array $carry, FieldInterface $field) {
            return array_merge($carry, $field->toArray());
        }, []);

        array_unshift($schema, 'SCHEMA', 'ADD');

        parent::setArguments(array_merge(
            [$index],
            $commandArguments,
            $schema
        ));
    }

    public function prefixKeys($prefix)
    {
        $this->applyPrefixForFirstArgument($prefix);
    }
}
