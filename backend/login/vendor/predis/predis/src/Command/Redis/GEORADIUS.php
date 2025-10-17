<?php











namespace Predis\Command\Redis;

use Predis\Command\PrefixableCommand as RedisCommand;









class GEORADIUS extends RedisCommand
{
    


    public function getId()
    {
        return 'GEORADIUS';
    }

    


    public function setArguments(array $arguments)
    {
        if ($arguments && is_array(end($arguments))) {
            $options = array_change_key_case(array_pop($arguments), CASE_UPPER);

            if (isset($options['WITHCOORD']) && $options['WITHCOORD'] == true) {
                $arguments[] = 'WITHCOORD';
            }

            if (isset($options['WITHDIST']) && $options['WITHDIST'] == true) {
                $arguments[] = 'WITHDIST';
            }

            if (isset($options['WITHHASH']) && $options['WITHHASH'] == true) {
                $arguments[] = 'WITHHASH';
            }

            if (isset($options['COUNT'])) {
                $arguments[] = 'COUNT';
                $arguments[] = $options['COUNT'];
            }

            if (isset($options['SORT'])) {
                $arguments[] = strtoupper($options['SORT']);
            }

            if (isset($options['STORE'])) {
                $arguments[] = 'STORE';
                $arguments[] = $options['STORE'];
            }

            if (isset($options['STOREDIST'])) {
                $arguments[] = 'STOREDIST';
                $arguments[] = $options['STOREDIST'];
            }
        }

        parent::setArguments($arguments);
    }

    public function prefixKeys($prefix)
    {
        if ($arguments = $this->getArguments()) {
            $arguments[0] = "$prefix{$arguments[0]}";
            $startIndex = $this->getId() === 'GEORADIUS' ? 5 : 4;

            if (($count = count($arguments)) > $startIndex) {
                for ($i = $startIndex; $i < $count; ++$i) {
                    switch (strtoupper($arguments[$i])) {
                        case 'STORE':
                        case 'STOREDIST':
                            $arguments[$i] = "$prefix{$arguments[++$i]}";
                            break;
                    }
                }
            }

            $this->setRawArguments($arguments);
        }
    }
}
