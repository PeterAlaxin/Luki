<?php
/**
 * Data adapter
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

use Luki\Data\BasicInterface;

abstract class BasicAdapter implements BasicInterface
{

    public $allLlastID = array();
    public $lastID = 0;
    public $allUpdated = array();
    public $updated = 0;
    public $allDeleted = array();
    public $deleted = 0;

    public function Select()
    {
        
    }

    public function Insert($table, $values)
    {
        $isFirst = true;
        $sql = 'INSERT INTO `' . $table . '` SET ';

        foreach ($values as $key => $value) {
            if (!$isFirst) {
                $sql .= ', ';
            } else {
                $isFirst = false;
            }

            $sql .= '`' . $key . '`="' . $this->escapeString($value) . '"';
        }

        $result = $this->Query($sql);

        if (false !== $result) {
            $this->saveLastID($table);
        }

        return $result;
    }

    public function Update($table, $values, $where = null)
    {
        $result = false;

        if (!empty($table) and ! empty($values)) {

            $isFirst = true;
            $sql = 'UPDATE `' . $table . '` SET ';

            foreach ($values as $key => $value) {
                if (!$isFirst) {
                    $sql .= ', ';
                } else {
                    $isFirst = false;
                }

                $sql .= '`' . $key . '`="' . $this->escapeString($value) . '"';
            }

            if (!empty($where)) {
                $isFirst = true;
                $sql .= ' WHERE ';

                foreach ($where as $key => $value) {
                    if (!$isFirst) {
                        $sql .= ' AND ';
                    } else {
                        $isFirst = false;
                    }

                    $sql .= '`' . $key . '`="' . $this->escapeString($value) . '"';
                }
            }

            $result = $this->Query($sql);
        }

        if (false !== $result) {
            $this->saveUpdated($table);
        }

        return $result;
    }

    public function Delete($table, $where)
    {
        $result = false;

        if (!empty($table)) {

            $sql = 'DELETE FROM `' . $table . '`';

            if (!empty($where)) {
                $isFirst = true;
                $sql .= ' WHERE ';

                foreach ($where as $key => $value) {
                    if (!$isFirst) {
                        $sql .= ' AND ';
                    } else {
                        $isFirst = false;
                    }

                    $sql .= '`' . $key . '`="' . $this->escapeString($value) . '"';
                }
            }

            $result = $this->Query($sql);
        }

        if (false !== $result) {
            $this->saveDeleted($table);
        }

        return $result;
    }

    public function getLastID($table = '')
    {
        if (empty($table)) {
            $lastId = $this->lastID;
        } elseif (isset($this->allLlastID[$table])) {
            $lastId = $this->allLlastID[$table];
        } else {
            $lastId = flase;
        }

        return $lastId;
    }

    public function getUpdated($table = '')
    {
        if (empty($table)) {
            $updated = $this->updated;
        } elseif (isset($this->allUpdated[$table])) {
            $updated = $this->allUpdated[$table];
        } else {
            $updated = false;
        }

        return $updated;
    }

    public function getDeleted($table = '')
    {
        if (empty($table)) {
            $deleted = $this->deleted;
        } elseif (isset($this->allDeleted[$table])) {
            $deleted = $this->allDeleted[$table];
        } else {
            $deleted = false;
        }

        return $deleted;
    }

    public function getFoundRows()
    {
        $result = $this->Query('SELECT FOUND_ROWS() AS foundRows;');
        $found = $result->Get('foundRows');

        return $found;
    }

    public function getStructure($table)
    {
        $result = $this->Query('SHOW FULL COLUMNS FROM ' . $table);

        return $result;
    }
}
