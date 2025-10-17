<?php











namespace Predis\Command\Traits;

use Predis\Command\Command;




trait Replace
{
    public function setArguments(array $arguments)
    {
        $replace = array_pop($arguments);

        if (is_bool($replace) && $replace) {
            $arguments[] = 'REPLACE';
        } elseif (!is_bool($replace)) {
            $arguments[] = $replace;
        }

        parent::setArguments($arguments);
    }
}
