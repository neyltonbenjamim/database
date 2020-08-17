<?php
/**
 * Record Banco de dados
 * Description
 * @version    1.0
 * @package    database
 * @author     Neylton Benjamim
 * @copyright (c) 19/03/2020 11:43, Neylton Benjamim
 */

namespace Database;

use Database\Sql\Select;
use Database\Sql\Insert;
use Database\Sql\Update;
use Database\Sql\Delete;

use Exception;

abstract class Record
{

    protected $data;
    protected $vdata;
    protected $attributes;

    public function __construct($id = null)
    {
        $this->attributes = array();

        if ($id) {
            $object = $this->load($id);
            if ($object) {
                $this->fromArray($object->toArray());
            }
        }
    }

    public function __get($property)
    {
        if (method_exists($this, 'get_' . $property)) {

            return call_user_func(array($this, 'get_' . $property));
        } else {
            if (isset($this->data[$property])) {

                return $this->data[$property];
            } else if (isset($this->vdata[$property])) {

                return $this->vdata[$property];
            }
        }
    }

    public function __set($property, $value)
    {
        if ($property == 'data') {
            echo 'Palavra resevada!';
        }
        if (method_exists($this, 'set_' . $property)) {

            call_user_func(array($this, 'set_' . $property), $value);
        } else {
            if ($value === NULL) {
                $this->data[$property] = NULL;
            }else if (is_scalar($value)) {
                $this->data[$property] = $value;
                unset($this->vdata[$property]);
                
            } else {
                $this->vdata[$property] = $value;
                unset($this->data[$property]);               
            }
        } 
        
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    public function __clone()
    {
        $pk = $this->getPrimaryKey();
        unset($this->$pk);
    }
    
    public function reload()
    {
        $pk = $this->getPrimaryKey(); 
        return $this->load($this->$pk);
    }

    public function load($id)
    {
        $class = get_class($this);
        $pk = $this->getPrimaryKey();

        $sql = new Select();
        $sql->setEntity($this->getEntity());
        $sql->addColumn($this->getAttributeList());

        $criteria = new Criteria;;
        $criteria->add(new Filter($pk, '=', $id));
        $sql->setCriteria($criteria);

        if ($conn = Transaction::get()) {
            $result = $conn->prepare($sql->getInstruction());
            $criteria->bind($result)->execute();
            $result = $result->fetchObject(static::class);
            return $result;
        } else {
            throw new Exception('Não foi encontrada a conexão');
        }
    }

    public function store()
    {
        $class = get_class($this);
        $pk = $this->getPrimaryKey();
        if (empty($this->data[$pk]) or ! self::exists($this->$pk)) {

            $sql = new Insert;
            $sql->setEntity($this->getEntity());

            foreach ($this->data as $key => $value) {
                $sql->setRowData($key, $this->data[$key]);
            }
        } else {

            $sql = new Update;
            $sql->setEntity($this->getEntity());
            $criteria = new Criteria;
            $criteria->add(new Filter($pk, '=', $this->$pk));
            $sql->setCriteria($criteria);
            foreach ($this->data as $key => $value) {
                if ($key !== $pk) {
                    $sql->setRowData($key, $this->data[$key]);
                }
            }
        }
        if ($conn = Transaction::get()) {
            $result = $conn->prepare($sql->getInstruction());
            $result = $sql->bind($result);
            $result->execute();
            if(self::exists($this->$pk)){
                $result = ['row' => $result->rowCount()];
            }else{
                $result = ['id' => $conn->lastInsertId()];
            }
        } else {
            throw new Exception('Não foi encontrada a conexão');
        }
        return $result;
    }

    public function delete($id = null)
    {
        $class = get_class($this);
        $pk = $this->getPrimaryKey();
        $id = $id ? $id : $this->$pk;

        $sql = new Delete;
        $sql->setEntity($this->getEntity());

        $criteria = new Criteria;
        $criteria->add(new Filter($pk, '=', $id));

        $sql->setCriteria($criteria);

        $conn = Transaction::get();
        $result = $conn->prepare($sql->getInstruction());
        $criteria->bind($result)->execute();
        return $result->rowCount();
    }

    public function exists($id)
    {
        if (empty($id)) {
            return FALSE;
        }

        return is_object(self::load($id));
    }

    protected function getEntity()
    {
        $class = get_class($this);
        return constant("{$class}::TABLENAME");
    }

    public function getPrimaryKey()
    {
        $class = get_class($this);
        return constant("{$class}::PRIMARYKEY");
    }

    public function fromArray($data)
    {
        if (count($this->attributes) > 0) {
            $pk = $this->getPrimaryKey();
            foreach ($data as $key => $value) {
                if ((in_array($key, $this->attributes) AND is_string($key)) OR ( $key === $pk)) {
                    $this->data[$key] = $data[$key];
                }
            }
        } else {
            foreach ($data as $key => $value) {
                $this->data[$key] = $data[$key];
            }
        }
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }
    

    public function toArray($filter_attributes = null)
    {
        $attributes = $filter_attributes ? $filter_attributes : $this->attributes;

        $data = array();
        if (count($attributes) > 0) {
            $pk = $this->getPrimaryKey();
            if (!empty($this->data)) {
                foreach ($this->data as $key => $value) {
                    if ((in_array($key, $attributes) AND is_string($key)) OR ( $key === $pk)) {
                        $data[$key] = $this->data[$key];
                    }
                }
            }
        } else {
            $data = $this->data;
        }
        return $data;
    }

    public function addAttribute($attribute)
    {
        if ($attribute == 'data') {
            throw new Exception('Atributo ERROR');
        }

        $this->attributes[] = $attribute;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttributeList()
    {
        if (count($this->attributes) > 0) {
            $attributes = $this->attributes;
            array_unshift($attributes, $this->getPrimaryKey());
            return implode(', ', array_unique($attributes));
        }

        return '*';
    }
    
    public static function create($data,$objectReturn = false)
    {
        $object = new static;
        $object->fromArray($data);
        $result = $object->store();
        if(!$objectReturn){
            return $result;
        }else{
            return $object;
        }
    }

    public static function all()
    {
        return self::getObjects();
    }

    public static function getObjects($criteria = null)
    {
        $classname = get_called_class();
        $repository = new Repository($classname);
        if (!$criteria) {
            $criteria = new Criteria;
        }

        return $repository->load($criteria);
    }

    public static function find($id)
    {
        $classname = get_called_class();
        $ar = new $classname;
        return $ar->load($id);
    }

    public static function where($variable, $operator, $value, $logicOperator = Expression::AND_OPERATOR)
    {
        $classname = get_called_class();
        $repository = new Repository($classname);
        return $repository->where($variable, $operator, $value, $logicOperator);
    }

    public static function orderBy($order, $direction = 'asc')
    {
        $class = get_called_class(); // get the Active Record class name
        $repository = new Repository($class); // create the repository
        return $repository->orderBy($order, $direction);
    }

    public static function groupBy($group)
    {
        $repository = new Repository( get_called_class() ); // create the repository
        return $repository->groupBy($group);
    }
}
