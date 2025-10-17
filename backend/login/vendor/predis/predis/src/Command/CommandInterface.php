<?php











namespace Predis\Command;




interface CommandInterface
{
    





    public function getId();

    




    public function setSlot($slot);

    




    public function getSlot();

    




    public function setArguments(array $arguments);

    




    public function setRawArguments(array $arguments);

    




    public function getArguments();

    






    public function getArgument($index);

    






    public function parseResponse($data);

    





    public function parseResp3Response($data);

    




    public function serializeCommand(): string;

    





    public static function deserializeCommand(string $serializedCommand): CommandInterface;
}
