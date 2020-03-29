<?php
/**
 * Update [TIPO]
 * Description
 * @package    database/sql
 * @author     Neylton Benjamim
 * @copyright (c) 19/03/2020 11:43, Neylton Benjamim
 */

namespace Database\Sql;

use Database\Sql\Statement;
use Database\Criteria;
use Exception;
use PDO;

final class Update extends Statement
{

    protected $sql;
    private $columnValues;
    private $preparedVars;

    public function __construct()
    {
        $this->columnValues = [];
        $this->preparedVars = [];
    }

    public function setRowData($column, $value)
    {
        if (is_scalar($value) OR is_null($value)) {
            $this->columnValues[$column] = $value;
        }
    }

    public function getPreparedVars()
    {
        if ($this->criteria) {
            return array_merge($this->preparedVars, $this->criteria->getPreparedVars());
        } else {
            return $this->preparedVars;
        }
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
        $this->sql = "UPDATE {$this->entity}";
        if ($this->columnValues) {
            foreach ($this->columnValues as $column => $value) {
                $value = $this->transform($value);
                $set[] = "{$column} = {$value}";
            }
        }
        $this->sql .= ' SET '.implode(', ', $set);
        
        if($this->criteria){
            $this->sql .= ' WHERE '.$this->criteria->dump();
        }

        return $this->sql;
    }
}
