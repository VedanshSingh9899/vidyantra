<?php
$redis_host = 'localhost';//gandmara host dal do
$redis_port = 6379;
//$redis_password = ''; agar kisi gandu ne access na kar le
$redsi = new Redis();
try {
    $redsi->connect($redis_host, $redis_port);
    //if (isset($redis_password)) {
    //    $redsi->auth($redis_password);
    //}
    if ($redis->ping('hello') !== 'hello') {
        throw new RedisException('Failed to connect to Redis: Invalid PONG received.');
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    die('Could not connect to Redis');
}
?>