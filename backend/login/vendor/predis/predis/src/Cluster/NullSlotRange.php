<?php











namespace Predis\Cluster;




class NullSlotRange extends SlotRange
{
    public function __construct(int $start, int $end)
    {
        parent::__construct($start, $end, '');
    }

    


    public function toArray(): array
    {
        return [];
    }

    


    public function count(): int
    {
        return 0;
    }
}
