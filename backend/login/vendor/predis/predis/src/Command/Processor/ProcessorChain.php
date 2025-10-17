<?php











namespace Predis\Command\Processor;

use ArrayAccess;
use ArrayIterator;
use InvalidArgumentException;
use Predis\Command\CommandInterface;
use ReturnTypeWillChange;
use Traversable;




class ProcessorChain implements ArrayAccess, ProcessorInterface
{
    private $processors = [];

    


    public function __construct($processors = [])
    {
        foreach ($processors as $processor) {
            $this->add($processor);
        }
    }

    


    public function add(ProcessorInterface $processor)
    {
        $this->processors[] = $processor;
    }

    


    public function remove(ProcessorInterface $processor)
    {
        if (false !== $index = array_search($processor, $this->processors, true)) {
            unset($this[$index]);
        }
    }

    


    public function process(CommandInterface $command)
    {
        for ($i = 0; $i < $count = count($this->processors); ++$i) {
            $this->processors[$i]->process($command);
        }
    }

    


    public function getProcessors()
    {
        return $this->processors;
    }

    




    public function getIterator()
    {
        return new ArrayIterator($this->processors);
    }

    




    public function count()
    {
        return count($this->processors);
    }

    



    #[ReturnTypeWillChange]
    public function offsetExists($index)
    {
        return isset($this->processors[$index]);
    }

    



    #[ReturnTypeWillChange]
    public function offsetGet($index)
    {
        return $this->processors[$index];
    }

    




    #[ReturnTypeWillChange]
    public function offsetSet($index, $processor)
    {
        if (!$processor instanceof ProcessorInterface) {
            throw new InvalidArgumentException(
                'Processor chain accepts only instances of `Predis\Command\Processor\ProcessorInterface`'
            );
        }

        $this->processors[$index] = $processor;
    }

    



    #[ReturnTypeWillChange]
    public function offsetUnset($index)
    {
        unset($this->processors[$index]);
        $this->processors = array_values($this->processors);
    }
}
