<?php
$x = 0;
for ($i = 0; $i < 1000; $i++) $x += sqrt($i);
try {
    $pdo = new PDO('mysql:host=mysql;port=3306;dbname=bench_db', 'bench_db', 'LbiAaSBhGHwXGcWx');
    $dbOk = $pdo->query("SELECT 1")->fetchColumn();
} catch (Exception $e) {
    $dbOk = 'error';
}
header('Content-Type: application/json');
echo json_encode(['status'=>'ok','compute'=>$x,'db'=>$dbOk,'php'=>PHP_VERSION]);
