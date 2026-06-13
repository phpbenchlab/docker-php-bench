<?php
$http = new Swoole\Http\Server("0.0.0.0", 9501);
$http->set([
    'worker_num' => 20,
    'enable_coroutine' => true,
    'hook_flags' => SWOOLE_HOOK_ALL,
]);
$http->on("request", function($req, $res) {
    $x = 0;
    for ($i = 0; $i < 1000; $i++) $x += sqrt($i);
    try {
        $pdo = new PDO('mysql:host=mysql;port=3306;dbname=bench_db', 'bench_db', 'LbiAaSBhGHwXGcWx');
        $dbOk = $pdo->query("SELECT 1")->fetchColumn();
    } catch (Exception $e) {
        $dbOk = 'error';
    }
    $res->header('Content-Type', 'application/json');
    $res->end(json_encode(['status'=>'ok','compute'=>$x,'db'=>$dbOk,'php'=>PHP_VERSION]));
});
$http->start();
