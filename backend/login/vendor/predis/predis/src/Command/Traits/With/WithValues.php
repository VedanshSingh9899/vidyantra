<?php











namespace Predis\Command\Traits\With;

use Predis\Command\Command;




trait WithValues
{
    public function setArguments(array $arguments)
    {
        $withValues = array_pop($arguments);

        if (is_bool($withValues) && $withValues) {
            $arguments[] = 'WITHVALUES';
        } elseif (!is_bool($withValues)) {
            $arguments[] = $withValues;
        }

        parent::setArguments($arguments);
    }
}
