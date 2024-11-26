<?php
/**
 * Repository [TIPO]
 * Description
 * @version    1.0
 * @package    database
 * @author     Neylton Benjamim
 * @copyright (c) 22/03/2020 18:00, Neylton Benjamim
 */

namespace Database;

use Database\Criteria;
use Database\Filter;

use Database\Sql\Select;
use Database\Sql\Update;

use Exception;

class Repository
{

    protected $class;
    protected $criteria;
    protected $setValues;
    protected $columns;

    public function __construct($class)
    {
        $this->class = $class;
        $this->criteria = new Criteria;
    }

    protected function getEntity()
    {
        return constant($this->class . '::TABLENAME');
    }

    protected function getAttributeList()
    {
        if (!empty($this->columns)) {
            return implode(', ', $this->columns);
        } else {
            $object = new $this->class;
            return $object->getAttributeList();
        }
    }

    public function select($columns)
    {
        $this->columns = $columns;
        return $this;
    }

    public function where($variable, $operator, $value, $logicOperator = Expression::AND_OPERATOR)
    {
        $this->criteria->add(new Filter($variable, $operator, $value), $logicOperator);

        return $this;
    }

    public function set($column, $value)
    {
        if (is_scalar($value) OR is_null($value)) {
            $this->setValues[$column] = $value;
        }

        return $this;
    }

    public function orWhere($variable, $operator, $value)
    {
        $this->criteria->add(new Filter($variable, $operator, $value), Expression::OR_OPERATOR);

        return $this;
    }

    public function orderBy($order, $direction = 'asc')
    {
        $this->criteria->setProperty('order', $order);
        $this->criteria->setProperty('direction', $direction);

        return $this;
    }

    public function groupBy($group)
    {
        $this->criteria->setProperty('group', $group);
        
        return $this;
    }
    
    public function take($limit)
    {
        $this->criteria->setProperty('limit', $limit);
        
        return $this;
    }

    public function skip($offset)
    {
        $this->criteria->setProperty('offset', $offset);
        
        return $this;
    }
    
    public function load(?Criteria $criteria = NULL )
    {
        if(!$criteria){
            $criteria = isset($this->criteria) ? $this->criteria : new Criteria;
        }
        
        $sql = new Select;
        $sql->addColumn($this->getAttributeList());
        $sql->setEntity($this->getEntity());
        $sql->setCriteria($criteria);
        if($conn = Transaction::get()){
            $rows = array();
            $result = $conn->prepare($sql->getInstruction());
            $criteria->bind($result)->execute();
            while($raw = $result->fetchObject($this->class)){
               $rows[] = $raw;
            }
            
            return $rows;
            
        }else{
            throw new Exception('Não tem transação ativa!');
        }
    }
    
    public function loadStatic()
    {
        return $this->load();
    }
    
    public function update($setValues = null, ?Criteria $criteria = null)
    {
        if(!$criteria){
            $criteria = isset($this->criteria) ? $this->criteria : new Criteria;
        }
        
        $setValues = isset($setValues) ? $setValues : $this->setValues;
        
        $class = $this->class;
        
        if($conn = Transaction::get()){
            
            $sql = new Update;
            if($setValues){
                foreach ($setValues as $column => $value){
                    $sql->setRowData($column, $value);
                }
            }
            
            $sql->setEntity($this->getEntity());
            $sql->setCriteria($criteria);

            $result = $conn->prepare($sql->getInstruction());
            $result = $sql->bind($result);
            $result->execute();
            
            
        }

        return $result;
    }
}
