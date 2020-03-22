<?php
/**
 * Expression [TIPO]
 * Description
 * @version    1.0
 * @package    database
 * @author     Neylton Benjamim
 * @copyright (c) 19/03/2020 11:43, Neylton Benjamim
 */

namespace Database;

abstract class Expression
{

    const AND_OPERATOR = 'AND ';
    const OR_OPERATOR = 'OR ';

    abstract public function dump();
}
