<?php
require __DIR__.'/config.php';

use \Database\Connection;

$conn = Connection::open([
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'dbname' => 'databasesv2'
]);

$conn = Connection::open([
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'dbname' => 'databasesv2'
]);
echo 'Passando array com os parametros da conexão';
var_dump($conn);
echo '<hr>';

$conn = Connection::open('databasesv3');
echo 'Buscando pelo nome do banco de dados';
var_dump($conn);
echo '<hr>';

$conn = Connection::open();
echo 'Banco default';
var_dump($conn);
echo '<hr>';

$result = $conn->query('SELECT * FROM tab_user');
var_dump($result->fetchAll());