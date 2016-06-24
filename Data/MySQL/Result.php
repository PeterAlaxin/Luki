<?php

/**
 * MySQL Result Iterator class
 *
 * Luki framework
 * Date 9.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki\Data\MySQL;

/**
 * MySQL Result Iterator class
 *
 * @package Luki
 */
class Result implements \Iterator
{

    public $position = 0;
    public $result = NULL;
    public $row = NULL;
    public $numberOfRecords = 0;

    public function rewind()
    {
        $this->position = 0;
        $this->_setRecord();

        return $this;
    }

    public function current()
    {
        return $this->row;
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        $this->position++;
        $this->_setRecord();

        return $this;
    }

    public function valid()
    {
        return (boolean) $this->row;
    }

    public function first()
    {
        $this->position = 0;
        $this->_setRecord();

        return $this;
    }

    public function last()
    {
        $this->position = $this->numberOfRecords - 1;
        $this->_setRecord();

        return $this;
    }

    public function getNumberOfRecords()
    {
        return $this->numberOfRecords;
    }

    public function Get($key)
    {
        $value = NULL;
        if ( isset($this->row[$key]) ) {
            $value = $this->row[$key];
        }

        unset($key);
        return $value;
    }

    public function getAllRows()
    {
        $allRows = array();

        foreach ( $this as $row ) {
            $allRows[] = $row;
        }

        unset($row);
        return $allRows;
    }

    public function getRow()
    {
        return $this->row;
    }

}

# End of file