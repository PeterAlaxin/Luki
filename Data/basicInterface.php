<?php

/**
 * Data Adapter interface
 *
 * Luki framework
 * Date 9.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki\Data;

/**
 * Data Adapter interface
 * 
 * @package Luki
 */
interface basicInterface
{

    public function __construct($options);

    public function Query($sql);

    public function Select();

    public function Insert($table, $values);

    public function Update($table, $values, $where);

    public function Delete($table, $where);

    public function getLastID($table);

    public function getUpdated($table);

    public function getDeleted($table);

    public function escapeString($string);

    public function saveLastID($table);

    public function saveUpdated($table);

    public function saveDeleted($table);
    
    public function getStructure($table);    
}

# End of file