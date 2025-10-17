<?php











namespace Predis\Configuration;









class Options implements OptionsInterface
{
    
    protected $handlers = [
        'aggregate' => Option\Aggregate::class,
        'cluster' => Option\Cluster::class,
        'replication' => Option\Replication::class,
        'connections' => Option\Connections::class,
        'commands' => Option\Commands::class,
        'exceptions' => Option\Exceptions::class,
        'prefix' => Option\Prefix::class,
        'crc16' => Option\CRC16::class,
    ];

    
    protected $options = [];

    
    protected $input;

    


    public function __construct(?array $options = null)
    {
        $this->input = $options ?? [];
    }

    


    public function getDefault($option)
    {
        if (isset($this->handlers[$option])) {
            $handler = $this->handlers[$option];
            $handler = new $handler();

            return $handler->getDefault($this);
        }
    }

    


    public function defined($option)
    {
        return
            array_key_exists($option, $this->options)
            || array_key_exists($option, $this->input)
        ;
    }

    


    public function __isset($option)
    {
        return (
            array_key_exists($option, $this->options)
            || array_key_exists($option, $this->input)
        ) && $this->__get($option) !== null;
    }

    


    public function __get($option)
    {
        if (isset($this->options[$option]) || array_key_exists($option, $this->options)) {
            return $this->options[$option];
        }

        if (isset($this->input[$option]) || array_key_exists($option, $this->input)) {
            $value = $this->input[$option];
            unset($this->input[$option]);

            if (isset($this->handlers[$option])) {
                $handler = $this->handlers[$option];
                $handler = new $handler();
                $value = $handler->filter($this, $value);
            } elseif (is_object($value) && method_exists($value, '__invoke')) {
                $value = $value($this);
            }

            return $this->options[$option] = $value;
        }

        if (isset($this->handlers[$option])) {
            return $this->options[$option] = $this->getDefault($option);
        }

        return;
    }

    


    public function __set($option, $value)
    {
        $this->options[$option] = $value;
    }
}
