<?php
/**
 * Connection
 * Description Class para abrir conexão com o banco de dados
 * @version    1.0
 * @package    database
 * @author     Neylton Benjamim
 * @copyright (c) 19/03/2020 11:43, Neylton Benjamim
 */

namespace Database;

use PDOException;
use PDO;

final class Connection
{

    private static $Host;
    private static $User;
    private static $Pass;
    private static $DBName;
    private static $Data;

    public static function open($database = null)
    {
        if(is_string($database) && !empty($database)){
            if(isset(DBS[$database])){
                self::$Data = DBS[$database];
            }else{
                throw new PDOException('Nome do Banco de dados não encontrado!');
            }
        }else if(is_array($database)){
            if(isset($database['host']) && isset($database['user']) && isset($database['pass']) && isset($database['dbname'])){
                self::$Data = $database;
            }else{
                throw new PDOException('Parametros incorreto');
            }
        }else{
            self::$Data = DBS[array_keys(DBS)[0]];   
        }
        
        self::$Host = self::$Data['host'];
        self::$User = self::$Data['user'];
        self::$Pass = self::$Data['pass'];
        self::$DBName = self::$Data['dbname'];

        $dsn = 'mysql:host=' . self::$Host.';dbname='.self::$DBName;
        $options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'];
        $conn = new PDO($dsn, self::$User, self::$Pass, $options);


        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $conn;
    }
   
}
