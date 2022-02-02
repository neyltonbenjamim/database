<?php
require __DIR__.'/config.php';

use Database\Record;
use Database\Transaction;

class user extends Record
{   
    const TABLENAME = 'tab_user';
    const PRIMARYKEY = 'user_id';


}

Transaction::open();

$user = new user(2);
var_dump($user);



