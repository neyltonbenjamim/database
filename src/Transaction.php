<?php
/**
 * Transaction [TIPO]
 * Description
 * @copyright (c) 19/03/2020, Neylton Benjamim
 * @author Neylton Benjamim
 */

namespace Database;

use \Database\Connection;

class Transaction
{

    private static $conn;

    private function __construct()
    {
        
    }

    public static function open($database = null)
    {
        if (empty(self::$conn)) {
            self::$conn = Connection::open($database);
            // Inicia a transação
            self::$conn->beginTransaction();
        }
    }

    public static function get()
    {
        return self::$conn;
    }

    public static function rollback()
    {
        if (self::$conn) {
            self::$conn->rollback();
            self::$conn = NULL;
        }
    }

    public static function close()
    {
        if (self::$conn) {
            // Finalizar a transação
            self::$conn->commit();
            self::$conn = NULL;
        }
    }

    public static function userDatabase($database)
    {
        if(self::$conn){
            self::$conn->exec("use `{$database}`;");
        }
        
    }
}
