<?php
/**
 * Select [TIPO]
 * Description
 * @package    database/sql
 * @author     Neylton Benjamim
 * @copyright (c) 19/03/2020 11:43, Neylton Benjamim
 */

namespace Database\Sql;

use Database\Sql\Statement;

final class Select extends Statement
{
    private $columns;
    
    public function addColumn($column)
    {
        $this->columns[] = $column;
    }

    public function getInstruction()
    {
        $this->sql = 'SELECT ';
        $this->sql .= implode(',', $this->columns);
        $this->sql .= ' FROM ' . $this->entity;

        if ($this->criteria) {
            $expression = $this->criteria->dump();
            if ($expression) {
                $this->sql .= ' WHERE ' . $expression;
            }

            $order = $this->criteria->getProperty('order');
            $group     = $this->criteria->getProperty('group');
            $limit = (int) $this->criteria->getProperty('limit');
            $offset = (int) $this->criteria->getProperty('offset');
            $direction = in_array($this->criteria->getProperty('direction'), array('asc', 'desc')) ? $this->criteria->getProperty('direction') : '';
            
            if($group) {
                $this->sql .= ' GROUP BY ' . $group;
            }
            if ($order) {
                $this->sql .= ' ORDER BY ' . $order . ' ' . $direction;
            }
            if ($limit) {
                $this->sql .= ' LIMIT ' . $limit;
            }
            if ($offset) {
                $this->sql .= ' OFFSET ' . $offset;
            }
        }
      
        return $this->sql;
    }
}
