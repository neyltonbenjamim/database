# Database
Gerenciamentos de bancos de dados MySQL
# Instalando database
```shell
composer require neylton/database
```
# Criando arquivo de configuração
## config.php
```php
define('DBS',array(

    'databasesv1' => array(
        'host'   => 'localhost',
        'user'   => 'root',
        'pass'   => '',
        'dbname' => 'databasesv1',
        'PREFIXTABLE' => 'tab'
    )
));
```

# Arquivo da aplicação 
## index.php

```php
<?php
//Puxando autoload do composer
require __DIR__.'/vendor/autoload.php';
//Puxando configuração do banco de dados
require __DIR__.'/config.php';

use Database\Transaction;
use Database\Record;
//Class user, para persistir user no banco de dados
class user extends Record
{
    const TABLENAME = 'tab_user';
    const PRIMARYKEY = 'user_id';
}
//Abrindo conexão com o banco de dados
Transaction::open();
//Puxando todos usuário do banco de dados
$users = user::all();

foreach ($users as $key => $user) {
	var_dump($user);
}
```
