<?php
require __DIR__.'/config.php';

use Database\Register;

class user extends Register
{
    public function load()
    {
        echo 'user load';
    }
}

$user = new user();
