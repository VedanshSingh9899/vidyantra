<?php











namespace Predis\Connection;




interface FactoryInterface
{
    





    public function define($scheme, $initializer);

    




    public function undefine($scheme);

    






    public function create($parameters);
}
