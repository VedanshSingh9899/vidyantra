<?php











namespace Predis\Command\Traits;

trait BitByte
{
    private static $argumentEnum = [
        'bit' => 'BIT',
        'byte' => 'BYTE',
    ];

    public function setArguments(array $arguments)
    {
        $value = array_pop($arguments);

        if (null === $value) {
            parent::setArguments($arguments);

            return;
        }

        if (in_array(strtoupper($value), self::$argumentEnum, true)) {
            $arguments[] = self::$argumentEnum[$value];
        } else {
            $arguments[] = $value;
        }

        parent::setArguments($arguments);
    }
}
