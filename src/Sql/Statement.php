<?php

/**
 * Statement [TIPO]
 * Description
 * @package    database/sql
 * @author     Neylton Benjamim
 * @copyright (c) 19/03/2020 11:43, Neylton Benjamim
 */

namespace Database\Sql;

use Database\Criteria;

abstract class Statement
{
    protected $sql;
    /** @var Criteria */
    protected $criteria;
    protected $entity;
    
    final public function setEntity($entity)
    {
        $this->entity = $entity;
    }
    
    final public function getEntity()
    {
        return $this->entity;
    }
    
    public function setCriteria(Criteria $criteria)
    {
        $this->criteria = $criteria;

    }
    
    protected function getRandomParameter()
    {
        return mt_rand(1000000000, 1999999999);
    }
    
    abstract function getInstruction();
}
