<?php
/**
 * Select [TIPO]
 * Description
 * @version    1.0
 * @package    database/sql
 * @author     Neylton Benjamim
 * @copyright (c) 19/03/2020 11:43, Neylton Benjamim
 */

namespace Database\Sql;

use Database\Sql\Statement;

final class Delete extends Statement
{
    protected $sql;
    protected $criteria;


    public function getInstruction()
    {
        $this->sql = "DELETE FROM {$this->entity}";
       

        if ($this->criteria) {
            $expression = $this->criteria->dump();
            if ($expression) {
                $this->sql .= ' WHERE ' . $expression;
            }
        }
        
        return $this->sql;
    }
}
