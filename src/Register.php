<?php

namespace Database;

use Database\Transaction;

/**
 * Register Base para class da tabela do banco
 * Description
 * @version    1.0
 * @package    database
 * @author     Neylton Benjamim
 * @copyright (c) 21/03/2020 23:48, Neylton Benjamim
 */

 class Register
 {

    protected $data;
    protected $vdata;
    protected $attributes;

    public function __construct($id = null, $loadRegister = true)
    {
        if($id){
            if($loadRegister){
                $object = $this->load($id);
            }else{
                $object = self::load($id);
            }

            if($object){

            }else{

            }
        }
       
    }

    public function __get($property)
    {
        

    }

    public function __set($property, $value)
    {

    }

    public function load(){
        
    }

    public function update()
    {

    }

    public function save()
    {
        
    }

 }