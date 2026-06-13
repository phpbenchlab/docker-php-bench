<?php
require __DIR__ . '/vendor/autoload.php';
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Worker;

$worker = Worker::create();
$psrFactory = new Psr17Factory();
$psr7 = new PSR7Worker($worker, $psrFactory, $psrFactory, $psrFactory);

while (true) {
    try {
        $request = $psr7->waitRequest();
        if ($request === null) break;
    } catch (\Throwable $e) {
        $psr7->respond(new Response(400));
        continue;
    }
    $x = 0;
    for ($i = 0; $i < 1000; $i++) $x += sqrt($i);
    try {
        $pdo = new PDO('mysql:host=mysql;port=3306;dbname=bench_db', 'bench_db', 'LbiAaSBhGHwXGcWx');
        $dbOk = $pdo->query("SELECT 1")->fetchColumn();
    } catch (Exception $e) {
        $dbOk = 'error';
    }
    $payload = json_encode(['status'=>'ok','compute'=>$x,'db'=>$dbOk,'php'=>PHP_VERSION]);
    $response = new Response(200, ['Content-Type' => 'application/json'], $payload);
    $psr7->respond($response);
}
