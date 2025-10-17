<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as BaseCommand;




class COMMAND extends BaseCommand
{
    


    public function getId()
    {
        return 'COMMAND';
    }

    


    public function parseResponse($data)
    {
        
        

        return $data;
    }
}
