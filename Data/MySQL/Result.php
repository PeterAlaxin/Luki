<?php
/**
 * MySQL Result Iterator class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Data
 * @filesource
 */

namespace Luki\Data\MySQL;

class Result implements \Iterator
{

    public $position = 0;
    public $result = null;
    public $row = null;
    public $numberOfRecords = 0;

    public function __destruct()
    {
        $this->position = null;
        $this->result = null;
        $this->row = null;
        $this->numberOfRecords = null;
    }

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
        if (isset($this->row[$key])) {
            $value = $this->row[$key];
        } else {
            $value = null;
        }

        return $value;
    }

    public function getAllRows()
    {
        $allRows = array();

        foreach ($this as $row) {
            $allRows[] = $row;
        }

        return $allRows;
    }

    public function getRow()
    {
        return $this->row;
    }
}
