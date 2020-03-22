<?php
/**
 * Insert [TIPO]
 * Description
 * @version    1.0
 * @package    database/sql
 * @author     Neylton Benjamim
 * @copyright (c) 19/03/2020 11:43, Neylton Benjamim
 */

namespace Database\Sql;

use Database\Sql\Statement;
use Database\Criteria;

use Exception;
use PDO;

final class Insert extends Statement
{

    protected $sql;
    private $columnValues;
    private $preparedVars;

    public function __construct()
    {
        $this->columnValues = [];
        $this->preparedVars = [];
    }

    public function setCriteria(Criteria $criteria)
    {
        throw new Exception('Não é preciso de <b>criteria</b> para inserir');
    }

    public function setRowData($column, $value)
    {
        if (is_scalar($value) OR is_null($value)) {
            $this->columnValues[$column] = $value;
        }
    }

    public function getPreparedVars()
    {
        return $this->preparedVars;
    }

    public function transform($value)
    {
        if (is_scalar($value)) {
            $result = $this->pdoParam($value);
        } else {
            foreach ($value as $v) {
                $foo[] = $this->pdoParam($v);
            }
            $result = '(' . implode(', ', $foo) . ')';
        }

        return $result;
    }

    private function pdoParam($value)
    {
        $preparedVar = ':par_' . $this->getRandomParameter();
        switch (gettype($value)) {
            case 'string':
                $this->preparedVars[$preparedVar] = [$value, PDO::PARAM_STR];
                break;
            case 'integer':
            case 'double':
                $this->preparedVars[$preparedVar] = [$value, PDO::PARAM_INT];
                break;
            case 'boolean':
                $this->preparedVars[$preparedVar] = [$value, PDO::PARAM_BOOL];
                break;
            case 'NULL':
                $this->preparedVars[$preparedVar] = [$value, PDO::PARAM_NULL];
                break;
            default :
                throw new Exception('Tipo inválido');
        }

        return $preparedVar;
    }
    
    public function bind($conn)
    {
        foreach ($this->getPreparedVars() as $k => $v) {
            $conn->bindValue($k, $v[0], $v[1]);
        }
        return $conn;
    }

    public function getInstruction()
    {
        $this->preparedVars = array();
        $columnValues = $this->columnValues;
        if ($columnValues) {
            foreach ($columnValues as $key => $value) {
                $columnValues[$key] = $this->transform($value);
            }
        }
        
        $this->sql = "INSERT INTO {$this->entity} (";
        $this->sql .= implode(', ', array_keys($columnValues)).')';
        $this->sql .= ' VALUES ('. implode(', ', array_values($columnValues)).')';
        
        return $this->sql;
    }
}
