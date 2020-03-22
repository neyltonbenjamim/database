<?php
/**
 * Criteria [TIPO]
 * Description
 * @version    1.0
 * @package    database
 * @author     Neylton Benjamim
 * @copyright (c) 22/03/2020 11:43, Neylton Benjamim
 */

namespace Database;

use Database\Expression;

class Criteria extends Expression
{

    private $expressions;
    private $operators;
    private $properties;

    public function __construct()
    {
        $this->expressions = array();
        $this->operators = array();

        $this->properties['order']     = '';
        $this->properties['offset']    = 0;
        $this->properties['direction'] = '';
        $this->properties['group']     = '';
    }

    public function add(Expression $expression, $operator = self::AND_OPERATOR)
    {
        if (empty($this->expressions)) {
            $operator = NULL;
        }

        $this->expressions[] = $expression;
        $this->operators[] = $operator;
    }

    public function getPreparedVars()
    {
        $preparedVars = array();
        if (is_array($this->expressions)) {
            if (count($this->expressions) > 0) {
                foreach ($this->expressions as $expression) {
                    $preparedVars = array_merge($preparedVars, $expression->getPreparedVars());
                }
            }
        }

        return $preparedVars;
    }

    public function bind($conn)
    {
        foreach ($this->getPreparedVars() as $k => $v) {
            $conn->bindValue($k, $v[0], $v[1]);
        }
        return $conn;
    }

    public function dump()
    {

        if (is_array($this->expressions)) {
            if (count($this->expressions) > 0) {
                $result = '';
                foreach ($this->expressions as $i => $expression) {
                    $operator = $this->operators[$i];
                    $result .= $operator . $expression->dump() . ' ';
                }
                return $result;
            }
        }
    }

    public function setProperty($property, $value)
    {
        if (isset($value)) {
            $this->properties[$property] = $value;
        } else {
            $this->properties[$property] = null;
        }
    }

    public function getProperty($property)
    {
        if (isset($this->properties[$property])) {
            return $this->properties[$property];
        }
    }

    public function resetProperties()
    {
        $this->properties['limit'] = NULL;
        $this->properties['order'] = NULL;
        $this->properties['offset'] = NULL;
        $this->properties['group']  = NULL;
    }
}
