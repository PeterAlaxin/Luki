<?php
/**
 * RecordNoExist validator
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Validator
 * @filesource
 */

namespace Luki\Validator;

use Luki\Validator\BasicFactory;

class RecordNoExist extends BasicFactory
{
    public $data  = null;
    public $table = null;
    public $key   = null;
    public $where = null;

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setMessage('The record with ID="%value%" exists!');
    }

    public function isValid($value)
    {
        $this->isValid = false;

        $select = $this->data->Select();
        $select->from($this->table, array($this->key))
            ->where($this->key.'="?"', $value)
            ->limit(1);

        if (!empty($this->where)) {
            $select->where($this->where);
        }

        $result = $this->data->Query($select);

        if (0 == $result->getNumberOfRecords()) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%value%/', $value);
        }

        return $this->isValid;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setWhere($where)
    {
        $this->where = $where;
        return $this;
    }

    public function getWhere()
    {
        return $this->where;
    }
}