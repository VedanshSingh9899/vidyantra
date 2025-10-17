<?php











namespace Predis;

use Predis\Command\Argument\Geospatial\ByInterface;
use Predis\Command\Argument\Geospatial\FromInterface;
use Predis\Command\Argument\Search\AggregateArguments;
use Predis\Command\Argument\Search\AlterArguments;
use Predis\Command\Argument\Search\CreateArguments;
use Predis\Command\Argument\Search\DropArguments;
use Predis\Command\Argument\Search\ExplainArguments;
use Predis\Command\Argument\Search\ProfileArguments;
use Predis\Command\Argument\Search\SchemaFields\FieldInterface;
use Predis\Command\Argument\Search\SearchArguments;
use Predis\Command\Argument\Search\SugAddArguments;
use Predis\Command\Argument\Search\SugGetArguments;
use Predis\Command\Argument\Search\SynUpdateArguments;
use Predis\Command\Argument\Server\LimitOffsetCount;
use Predis\Command\Argument\Server\To;
use Predis\Command\Argument\TimeSeries\AddArguments;
use Predis\Command\Argument\TimeSeries\AlterArguments as TSAlterArguments;
use Predis\Command\Argument\TimeSeries\CreateArguments as TSCreateArguments;
use Predis\Command\Argument\TimeSeries\DecrByArguments;
use Predis\Command\Argument\TimeSeries\GetArguments;
use Predis\Command\Argument\TimeSeries\IncrByArguments;
use Predis\Command\Argument\TimeSeries\InfoArguments;
use Predis\Command\Argument\TimeSeries\MGetArguments;
use Predis\Command\Argument\TimeSeries\MRangeArguments;
use Predis\Command\Argument\TimeSeries\RangeArguments;
use Predis\Command\CommandInterface;
use Predis\Command\Container\ACL;
use Predis\Command\Container\CLIENT;
use Predis\Command\Container\FUNCTIONS;
use Predis\Command\Container\Json\JSONDEBUG;
use Predis\Command\Container\Search\FTCONFIG;
use Predis\Command\Container\Search\FTCURSOR;
use Predis\Command\Container\XGROUP;
use Predis\Command\Container\XINFO;
use Predis\Command\FactoryInterface;
use Predis\Command\Redis\VADD;
use Predis\Configuration\OptionsInterface;
use Predis\Connection\ConnectionInterface;
use Predis\Response\Status;






































































































































































































































































































































































interface ClientInterface
{
    




    public function getCommandFactory();

    




    public function getOptions();

    


    public function connect();

    


    public function disconnect();

    




    public function getConnection();

    







    public function createCommand($method, $arguments = []);

    






    public function executeCommand(CommandInterface $command);

    








    public function __call($method, $arguments);
}
