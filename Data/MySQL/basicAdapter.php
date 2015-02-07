<?php

/**
 * Data adapter
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

namespace Luki\Data\MySQL;

use Luki\Data\basicInterface;

/**
 * Data adapter
 * 
 * @package Luki
 */
abstract class basicAdapter implements basicInterface
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
        $isFirst = TRUE;
        $sql = 'INSERT INTO `' . $table . '` SET ';

        foreach ( $values as $key => $value ) {
            if ( !$isFirst ) {
                $sql .= ', ';
            } else {
                $isFirst = FALSE;
            }

            $sql .= '`' . $key . '`="' . $this->escapeString($value) . '"';
        }

        $result = $this->Query($sql);

        if ( FALSE !== $result ) {
            $this->saveLastID($table);
        }

        unset($table, $values, $key, $value, $sql, $isFirst);
        return $result;
    }

    public function Update($table, $values, $where)
    {
        $result = FALSE;

        if ( !empty($table) and ! empty($values) ) {

            $isFirst = TRUE;
            $sql = 'UPDATE `' . $table . '` SET ';

            foreach ( $values as $key => $value ) {
                if ( !$isFirst ) {
                    $sql .= ', ';
                } else {
                    $isFirst = FALSE;
                }

                $sql .= '`' . $key . '`="' . $this->escapeString($value) . '"';
            }

            if ( !empty($where) ) {
                $isFirst = TRUE;
                $sql .= ' WHERE ';

                foreach ( $where as $key => $value ) {
                    if ( !$isFirst ) {
                        $sql .= ' AND ';
                    } else {
                        $isFirst = FALSE;
                    }

                    $sql .= '`' . $key . '`="' . $this->escapeString($value) . '"';
                }
            }

            $result = $this->Query($sql);
        }

        if ( FALSE !== $result ) {
            $this->saveUpdated($table);
        }

        unset($table, $values, $where, $isFirst, $sql, $key, $value);
        return $result;
    }

    public function Delete($table, $where)
    {
        $result = FALSE;

        if ( !empty($table) ) {

            $sql = 'DELETE FROM `' . $table . '`';

            if ( !empty($where) ) {
                $isFirst = TRUE;
                $sql .= ' WHERE ';

                foreach ( $where as $key => $value ) {
                    if ( !$isFirst ) {
                        $sql .= ' AND ';
                    } else {
                        $isFirst = FALSE;
                    }

                    $sql .= '`' . $key . '`="' . $this->escapeString($value) . '"';
                }
            }

            $result = $this->Query($sql);
        }

        if ( FALSE !== $result ) {
            $this->saveDeleted($table);
        }

        unset($table, $where, $isFirst, $sql, $key, $value);
        return $result;
    }

    public function getLastID($table = '')
    {
        $lastId = FALSE;

        if ( empty($table) ) {
            $lastId = $this->lastID;
        } elseif ( isset($this->allLlastID[$table]) ) {
            $lastId = $this->allLlastID[$table];
        }

        unset($table);
        return $lastId;
    }

    public function getUpdated($table = '')
    {
        $updated = FALSE;

        if ( empty($table) ) {
            $updated = $this->updated;
        } elseif ( isset($this->allUpdated[$table]) ) {
            $updated = $this->allUpdated[$table];
        }

        unset($table);
        return $updated;
    }

    public function getDeleted($table = '')
    {
        $deleted = FALSE;

        if ( empty($table) ) {
            $deleted = $this->deleted;
        } elseif ( isset($this->allDeleted[$table]) ) {
            $deleted = $this->allDeleted[$table];
        }

        unset($table);
        return $deleted;
    }

    public function getFoundRows()
    {
        $result = $this->Query('SELECT FOUND_ROWS() AS foundRows;');
        $found = $result->Get('foundRows');
        
        unset($result);
        return $found;
    }
}

# End of file