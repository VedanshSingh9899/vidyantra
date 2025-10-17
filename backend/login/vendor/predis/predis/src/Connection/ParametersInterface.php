<?php











namespace Predis\Connection;




























interface ParametersInterface
{
    






    public function __isset($parameter);

    






    public function __get($parameter);

    




    public function __toString();

    




    public function toArray();
}
