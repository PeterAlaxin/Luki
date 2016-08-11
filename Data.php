<?php
/**
 * Data class
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

namespace Luki;

use Luki\Data\BasicInterface;

class Data
{

    private $adapter = null;

    public function __construct(BasicInterface $dataAdapter)
    {
        $this->adapter = $dataAdapter;
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public static function findAdapter($adapter)
    {
        $adapter = __NAMESPACE__ . '\Data\\' . $adapter . 'Adapter';

        return $adapter;
    }

    public function Select()
    {
        $select = $this->adapter->Select();

        return $select;
    }

    public function Insert($table, $values)
    {
        $lastId = $this->adapter->Insert($table, $values);

        return $lastId;
    }

    public function Update($table, $values, $where = null)
    {
        $result = $this->adapter->Update($table, $values, $where);

        return $result;
    }

    public function Delete($table, $where = null)
    {
        $result = $this->adapter->Delete($table, $where);

        return $result;
    }

    public function Query($select)
    {
        $result = $this->adapter->Query($select);

        return $result;
    }

    public function getLastID($table = '')
    {
        $lastId = $this->adapter->getLastID($table);

        return $lastId;
    }

    public function getUpdated($table = '')
    {
        $updated = $this->adapter->getUpdated($table);

        return $updated;
    }

    public function getDeleted($table = '')
    {
        $deleted = $this->adapter->getDeleted($table);

        return $deleted;
    }

    public function getFoundRows()
    {
        $found = $this->adapter->getFoundRows();

        return $found;
    }

    public function getStructure($table)
    {
        $structure = $this->adapter->getStructure($table);

        return $structure;
    }
}
