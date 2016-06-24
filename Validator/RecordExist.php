<?php

/**
 * RecordExist validator
 *
 * Luki framework
 * Date 14.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Validator
 * @filesource
 */

namespace Luki\Validator;

use Luki\Validator\basicFactory;

/**
 * RecordExist validator
 * 
 * @package Luki
 */
class RecordExist extends basicFactory
{

    public $data = NULL;
    public $table = NULL;
    public $key = NULL;

    public function __construct($options = array())
    {
        parent::__construct($options);

        $this->setMessage('The record with ID="%value%" does not exists!');

        unset($options);
    }

    public function isValid($value)
    {
        $this->isValid = FALSE;

        $select = $this->data->Select();
        $select->from($this->table, array( $this->key ))
                ->where($this->key . '=?', $value)
                ->limit(1);

        $result = $this->data->Query($select);

        if ( 1 == $result->getNumberOfRecords() ) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%value%/', $value);
        }

        unset($value, $select, $result);
        return $this->isValid;
    }

    public function setData($data)
    {
        $this->data = $data;

        unset($data);
    }

    public function getData()
    {
        return $this->data;
    }

    public function setTable($table)
    {
        $this->table = $table;

        unset($table);
    }

    public function getTable()
    {
        return $this->table;
    }

    public function setKey($key)
    {
        $this->key = $key;

        unset($key);
    }

    public function getKey()
    {
        return $this->key;
    }

}

# End of file