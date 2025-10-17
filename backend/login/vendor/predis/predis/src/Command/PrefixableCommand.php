<?php











namespace Predis\Command;

abstract class PrefixableCommand extends Command implements PrefixableCommandInterface
{
    


    abstract public function getId();

    


    abstract public function prefixKeys($prefix);

    





    public function applyPrefixForAllArguments(string $prefix): void
    {
        $this->setRawArguments(
            array_map(static function ($key) use ($prefix) {
                return $prefix . $key;
            }, $this->getArguments())
        );
    }

    





    public function applyPrefixForFirstArgument(string $prefix): void
    {
        $arguments = $this->getArguments();
        $arguments[0] = $prefix . $arguments[0];
        $this->setRawArguments($arguments);
    }

    





    public function applyPrefixForInterleavedArgument(string $prefix): void
    {
        if ($arguments = $this->getArguments()) {
            $length = count($arguments);

            for ($i = 0; $i < $length; $i += 2) {
                $arguments[$i] = "$prefix{$arguments[$i]}";
            }

            $this->setRawArguments($arguments);
        }
    }

    





    public function applyPrefixSkippingLastArgument(string $prefix): void
    {
        if ($arguments = $this->getArguments()) {
            $length = count($arguments);

            for ($i = 0; $i < $length - 1; ++$i) {
                $arguments[$i] = "$prefix{$arguments[$i]}";
            }

            $this->setRawArguments($arguments);
        }
    }

    





    public function applyPrefixSkippingFirstArgument(string $prefix): void
    {
        if ($arguments = $this->getArguments()) {
            $length = count($arguments);

            for ($i = 1; $i < $length; ++$i) {
                $arguments[$i] = "$prefix{$arguments[$i]}";
            }

            $this->setRawArguments($arguments);
        }
    }
}
