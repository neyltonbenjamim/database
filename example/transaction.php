<?php
require __DIR__.'/config.php';


use \Database\Transaction;

Transaction::open('databasesv1');

$conn = Transaction::get();

$result = $conn->query('SELECT * FROM tab_user');
var_dump($result->fetchAll());

Transaction::useDatabase('databasesv2');

$result = $conn->query('SELECT * FROM tab_user');
var_dump($result->fetchAll());

