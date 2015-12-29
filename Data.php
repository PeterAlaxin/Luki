<?php

/**
 * Data class
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

namespace Luki;

use Luki\Data\basicInterface;

/**
 * Data class
 *
 * Data access
 *
 * @package Luki
 */
class Data
{

    private $_dataAdapter = NULL;

    /**
     * Data constructor
     */
    public function __construct(basicInterface $dataAdapter)
    {
        $this->_dataAdapter = $dataAdapter;

        unset($dataAdapter);
    }

    public static function findAdapter($adapter)
    {
        $adapter = __NAMESPACE__ . '\Data\\' . $adapter . 'Adapter';

        return $adapter;
    }

    public function Select()
    {
        $select = $this->_dataAdapter->Select();

        return $select;
    }

    public function Insert($table, $values)
    {
        $lastId = $this->_dataAdapter->Insert($table, $values);

        unset($table, $values);
        return $lastId;
    }

    public function Update($table, $values, $where = NULL)
    {
        $result = $this->_dataAdapter->Update($table, $values, $where);

        unset($table, $values, $where);
        return $result;
    }

    public function Delete($table, $where = NULL)
    {
        $result = $this->_dataAdapter->Delete($table, $where);

        unset($table, $where);
        return $result;
    }

    public function Query($select)
    {
        $result = $this->_dataAdapter->Query($select);

        unset($select);
        return $result;
    }

    public function getLastID($table = '')
    {
        $lastId = $this->_dataAdapter->getLastID($table);

        unset($table);
        return $lastId;
    }

    public function getUpdated($table = '')
    {
        $updated = $this->_dataAdapter->getUpdated($table);

        unset($table);
        return $updated;
    }

    public function getDeleted($table = '')
    {
        $deleted = $this->_dataAdapter->getDeleted($table);

        unset($table);
        return $deleted;
    }

    public function getFoundRows()
    {
        $found = $this->_dataAdapter->getFoundRows();

        return $found;
    }

    public function getStructure($table)
    {
        $structure = $this->_dataAdapter->getStructure($table);
        
        unset($table);
        return $structure;
    }
}

# End of file