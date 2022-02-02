<?php
/**
 * Filter [TIPO]
 * Description
 * @version    1.0
 * @package    database
 * @author     Neylton Benjamim
 * @copyright (c) 19/03/2020 11:43, Neylton Benjamim
 */

namespace Database;

use Database\Expression;
use Exception;
use PDO;

class Filter extends Expression
{

    private $variable;
    private $operator;
    private $value;
    private $value2;
    private $preparedVars;

    /**
     * Class Constructor
     * 
     * @param  $variable = variable
     * @param  $operator = operator (>, <, =, BETWEEN)
     * @param  $value    = value to be compared
     * @param  $value2   = second value to be compared (between)
     */
    public function __construct($variable, $operator, $value, $value2 = null)
    {
        $this->variable = $variable;
        $this->operator = $operator;
        $this->preparedVars = array();

        $this->value = $value;

        if ($value2) {

            $this->value2 = $value2;
        }
    }

    public function getPreparedVars()
    {
        return $this->preparedVars;
    }

    public function transform($value)
    {
        if (is_scalar($value) OR is_null($value)) {
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

    public function dump()
    {
        $value = $this->transform($this->value);

        if ($this->value2) {

            $value2 = $this->transform($this->value2);
            return "{$this->variable} {$this->operator} {$value} AND {$value2}";
        } else {
            return "{$this->variable} {$this->operator} {$value}";
        }
    }

    private function getRandomParameter()
    {
        return mt_rand(1000000000, 1999999999);
    }
}
