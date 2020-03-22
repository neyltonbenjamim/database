# Database
Gerenciamentos de bancos de dados MySQL
```php
<?php
require __DIR__.'/vendor/autoload.php';

define('DBS',array(

    'databasesv1' => array(
        'host'   => 'localhost',
        'user'   => 'root',
        'pass'   => '',
        'dbname' => 'databasesv1',
        'PREFIXTABLE' => 'tab'
    )
    ));

use Database\Transaction;
use Database\Record;

class user extends Record
{
    const TABLENAME = 'tab_user';
    const PRIMARYKEY = 'user_id';
}
Transaction::open();
var_dump(user::all());
```
