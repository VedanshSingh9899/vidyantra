<?php











namespace Predis\Connection;

use InvalidArgumentException;






class Parameters implements ParametersInterface
{
    protected static $defaults = [
        'scheme' => 'tcp',
        'host' => '127.0.0.1',
        'port' => 6379,
        'protocol' => 2,
    ];

    





    protected $parameters;

    


    public function __construct(array $parameters = [])
    {
        $this->parameters = $this->filter($parameters + static::$defaults);
    }

    






    protected function filter(array $parameters)
    {
        return array_filter($parameters, function ($value) {
            return $value !== null && $value !== '';
        });
    }

    







    public static function create($parameters)
    {
        if (is_string($parameters)) {
            $parameters = static::parse($parameters);
        }

        return new static($parameters ?: []);
    }

    
















    public static function parse($uri)
    {
        if (stripos($uri, 'unix://') === 0) {
            
            
            $uri = str_ireplace('unix://', 'unix:', $uri);
        }

        if (!$parsed = parse_url($uri)) {
            throw new InvalidArgumentException("Invalid parameters URI: $uri");
        }

        if (
            isset($parsed['host'])
            && false !== strpos($parsed['host'], '[')
            && false !== strpos($parsed['host'], ']')
        ) {
            $parsed['host'] = substr($parsed['host'], 1, -1);
        }

        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $queryarray);
            unset($parsed['query']);

            $parsed = array_merge($parsed, $queryarray);
        }

        if (stripos($uri, 'redis') === 0) {
            if (isset($parsed['user'])) {
                if (strlen($parsed['user'])) {
                    $parsed['username'] = $parsed['user'];
                }
                unset($parsed['user']);
            }

            if (isset($parsed['pass'])) {
                if (strlen($parsed['pass'])) {
                    $parsed['password'] = $parsed['pass'];
                }
                unset($parsed['pass']);
            }

            if (isset($parsed['path']) && preg_match('/^\/(\d+)(\/.*)?/', $parsed['path'], $path)) {
                $parsed['database'] = $path[1];

                if (isset($path[2])) {
                    $parsed['path'] = $path[2];
                } else {
                    unset($parsed['path']);
                }
            }
        }

        return $parsed;
    }

    


    public function toArray()
    {
        return $this->parameters;
    }

    


    public function __get($parameter)
    {
        if (isset($this->parameters[$parameter])) {
            return $this->parameters[$parameter];
        }
    }

    public function __set($parameter, $value)
    {
        $this->parameters[$parameter] = $value;
    }

    


    public function __isset($parameter)
    {
        return isset($this->parameters[$parameter]);
    }

    


    public function __toString()
    {
        if ($this->scheme === 'unix') {
            return "$this->scheme:$this->path";
        }

        if (filter_var($this->host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return "$this->scheme://[$this->host]:$this->port";
        }

        return "$this->scheme://$this->host:$this->port";
    }

    


    public function __sleep()
    {
        return ['parameters'];
    }
}
