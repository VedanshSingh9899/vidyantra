<?php











namespace Predis\Command\Redis;

use Predis\Command\Command as RedisCommand;




class INFO extends RedisCommand
{
    


    public function getId()
    {
        return 'INFO';
    }

    


    public function parseResponse($data)
    {
        if (empty($data) || !$lines = preg_split('/\r?\n/', $data)) {
            return [];
        }

        if (strpos($lines[0], '#') === 0) {
            return $this->parseNewResponseFormat($lines);
        } else {
            return $this->parseOldResponseFormat($lines);
        }
    }

    


    public function parseNewResponseFormat($lines)
    {
        $info = [];
        $current = null;

        foreach ($lines as $row) {
            if ($row === '') {
                continue;
            }

            if (preg_match('/^# (\w+)$/', $row, $matches)) {
                $info[$matches[1]] = [];
                $current = &$info[$matches[1]];
                continue;
            }

            [$k, $v] = $this->parseRow($row);
            $current[$k] = $v;
        }

        return $info;
    }

    


    public function parseOldResponseFormat($lines)
    {
        $info = [];

        foreach ($lines as $row) {
            if (strpos($row, ':') === false) {
                continue;
            }

            [$k, $v] = $this->parseRow($row);
            $info[$k] = $v;
        }

        return $info;
    }

    






    protected function parseRow($row)
    {
        if (preg_match('/^module:name/', $row)) {
            return $this->parseModuleRow($row);
        }

        [$k, $v] = explode(':', $row, 2);

        if (preg_match('/^db\d+$/', $k)) {
            $v = $this->parseDatabaseStats($v);
        }

        return [$k, $v];
    }

    






    protected function parseDatabaseStats($str)
    {
        $db = [];

        foreach (explode(',', $str) as $dbvar) {
            [$dbvk, $dbvv] = explode('=', $dbvar);
            $db[trim($dbvk)] = $dbvv;
        }

        return $db;
    }

    





    protected function parseModuleRow(string $row): array
    {
        [$moduleKeyword, $moduleData] = explode(':', $row);
        $explodedData = explode(',', $moduleData);
        $parsedData = [];

        foreach ($explodedData as $moduleDataRow) {
            [$k, $v] = explode('=', $moduleDataRow);

            if ($k === 'name') {
                $parsedData[0] = $v;
                continue;
            }

            $parsedData[1][$k] = $v;
        }

        return $parsedData;
    }

    



    public function parseResp3Response($data)
    {
        return $this->parseResponse($data);
    }
}
