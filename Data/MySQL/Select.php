<?php
/**
 * MySQL Select class
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

class Select
{

    const ALL = 'all';
    const DISTINCT = 'distinct';
    const CACHE = 'cache';
    const CALC_FOUND_ROWS = 'calc';
    const JOIN = 'join';
    const GROUP = 'group';
    const WHERE = 'where';
    const ORDER = 'order';
    const LIMIT = 'limit';
    const UNION = 'union';

    private $parent = null;
    private $select = array();
    private $emptySelect = array(
        'distinct' => 'ALL',
        'cache' => '',
        'calc' => '',
        'columns' => array(),
        'tables' => array(),
        'from' => array(),
        'join' => array(),
        'group' => '',
        'having' => '',
        'where' => '',
        'orWhere' => array(),
        'order' => '',
        'limit' => array(
            'from' => 0,
            'count' => 0
        ),
        'union' => array()
    );

    public function __construct($parent)
    {
        $this->parent = $parent;

        $this->reset(self::ALL);
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function __toString()
    {
        $sql = $this->finalizeSelect();

        return $sql;
    }

    public function distinct()
    {
        $this->select['distinct'] = 'DISTINCT';

        return $this;
    }

    public function cache()
    {
        $this->select['cache'] = 'SQL_CACHE';

        return $this;
    }

    public function noCache()
    {
        $this->select['cache'] = 'SQL_NO_CACHE';

        return $this;
    }

    public function calcFoundRows()
    {
        $this->select['calc'] = 'SQL_CALC_FOUND_ROWS';

        return $this;
    }

    public function from($table, $columns = '*')
    {
        if (!is_array($table)) {
            $table = array($table => $table);
        }

        $this->select['from'] = $table;
        $table = $this->_addTable($table);
        $this->_addColumns($table, $columns);

        return $this;
    }

    public function join($table, $condition, $columns = '*')
    {
        $this->prepareJoin('inner', $table, $condition, $columns);

        return $this;
    }

    public function joinInner($table, $condition, $columns = '*')
    {
        $this->prepareJoin('inner', $table, $condition, $columns);

        return $this;
    }

    public function joinLeft($table, $condition, $columns = '*')
    {
        $this->prepareJoin('left', $table, $condition, $columns);

        return $this;
    }

    public function joinRight($table, $condition, $columns = '*')
    {
        $this->prepareJoin('right', $table, $condition, $columns);

        return $this;
    }

    public function where($condition, $parameter = null)
    {
        if (!empty($this->select['where'])) {
            $this->select['where'] .= ' AND ';
        }

        if (!is_null($parameter)) {
            $condition = $this->escapeString($condition, $parameter);
        }

        $this->select['where'] .= '(' . $condition . ')';

        return $this;
    }

    public function orWhere($condition, $parameter = null)
    {
        if (!is_null($parameter)) {
            $condition = $this->escapeString($condition, $parameter);
        }

        $this->select['orWhere'][] = $condition;

        return $this;
    }

    public function group($groups)
    {
        $groups = (array) $groups;

        foreach ($groups as $key => $group) {
            if ($key > 0) {
                $group = ', ' . $group;
            }
            $this->select['group'] .= $group;
        }

        return $this;
    }

    public function having($condition, $parameter = null)
    {
        if (!empty($this->select['having'])) {
            $this->select['having'] .= ' AND ';
        }

        if (!is_null($parameter)) {
            $condition = $this->_escape($condition, $parameter);
        }

        $this->select['having'] .= '(' . $condition . ')';

        return $this;
    }

    public function orHaving($condition, $parameter = null)
    {
        if (!empty($this->select['having'])) {
            $this->select['having'] = '(' . $this->select['having'] . ') OR ';
        }

        if (!is_null($parameter)) {
            $condition = $this->_escape($condition, $parameter);
        }

        $this->select['having'] .= '(' . $condition . ')';

        return $this;
    }

    public function order($orders)
    {
        $orders = (array) $orders;

        foreach ($orders as $order) {
            if (!empty($this->select['order'])) {
                $order = ', ' . $order;
            }
            $this->select['order'] .= $order;
        }

        return $this;
    }

    public function limit($from = 0, $count = null)
    {
        $this->select['limit']['from'] = (int) $from;

        if (!is_null($count)) {
            $this->select['limit']['count'] = (int) $count;
        }

        return $this;
    }

    public function page($page, $count)
    {
        $from = ($page - 1) * $count;
        $this->limit($from, $count);

        return $this;
    }

    public function reset($section)
    {
        if (isset($this->emptySelect[$section])) {
            $this->select[$section] = $this->emptySelect[$section];
        } elseif ('all' == $section) {
            $this->select = $this->emptySelect;
        }

        return $this;
    }

    public function union($sql)
    {
        if (is_array($sql)) {
            $this->select['union'] = $sql;
        }

        return $this;
    }

    private function _addTable($table)
    {
        if (is_array($table)) {

            foreach ($table as $key => $value) {
                $this->select['tables'][$key] = $value;
                $table = $key;
                break;
            }
        } else {
            $this->select['tables'][$table] = $table;
        }

        return $table;
    }

    private function _addColumns($table, $columns)
    {
        $columns = (array) $columns;

        foreach ($columns as $alias => $column) {
            if (!is_string($alias)) {
                $alias = $column;
            }

            $this->select['columns'][$table][$alias] = $column;
        }
    }

    public function prepareJoin($type, $table, $condition, $columns)
    {
        if (!is_array($table)) {
            $table = array($table => $table);
        }

        $this->select['join'][] = array(
            'type' => $type,
            'table' => $table,
            'condition' => $condition
        );

        $table = $this->_addTable($table);
        $this->_addColumns($table, $columns);
    }

    private function finalizeSelect()
    {
        #<editor-fold defaultstate="collapsed" desc="Union">
        if (!empty($this->select['union'])) {
            $sql = '';

            foreach ($this->select['union'] as $key => $select) {
                if ($key > 0) {
                    $sql .= ' UNION ALL ' . chr(13);
                }
                $sql .= 'SELECT * FROM ( ' . (string) $select . ' ) AS s' . $key . chr(13);
            }
        }
        #</editor-fold>
        #<editor-fold defaultstate="collapsed" desc="Single Select">
        else {
            $sql = 'SELECT ';

            #<editor-fold defaultstate="collapsed" desc="Distinct">
            $sql .= $this->select['distinct'] . ' ';
            $sql .= chr(13);
            #</editor-fold>
            #<editor-fold defaultstate="collapsed" desc="Cache">
            if (!empty($this->select['cache'])) {
                $sql .= $this->select['cache'] . ' ';
                $sql .= chr(13);
            }
            #</editor-fold>
            #<editor-fold defaultstate="collapsed" desc="Calc rows">
            if (!empty($this->select['calc'])) {
                $sql .= $this->select['calc'] . ' ';
                $sql .= chr(13);
            }
            #</editor-fold>
            #<editor-fold defaultstate="collapsed" desc="Columns">
            $columns = '';
            foreach ($this->select['columns'] as $table => $tableColumns) {
                $tableColumns = (array) $tableColumns;

                foreach ($tableColumns as $alias => $column) {
                    if (!empty($columns)) {
                        $columns .= ', ' . chr(13);
                    }

                    if (false === strstr($column, '(')) {
                        $columns .= $this->quote($table) . '.';
                        if ('*' == $column) {
                            $columns .= $column;
                        } else {
                            $columns .= $this->quote($column);
                        }
                    } else {
                        $columns .= $column;
                    }

                    if ($alias != $column) {
                        $columns .= ' AS ' . $this->quote($alias);
                    }
                }
            }
            $sql .= $columns . chr(13);
            #</editor-fold>

            $sql .= ' FROM ';
        }
        #</editor-fold>
        #<editor-fold defaultstate="collapsed" desc="From">
        if (!empty($this->select['from'])) {
            foreach ($this->select['from'] as $alias => $realTable) {
                $sql .= $this->quote($realTable);

                if ($realTable != $alias) {
                    $sql .= ' AS ' . $this->quote($alias);
                }
                break;
            }
            $sql .= chr(13);
        }
        #</editor-fold>
        #<editor-fold defaultstate="collapsed" desc="Join">
        if (!empty($this->select['join'])) {
            foreach ($this->select['join'] as $join) {

                switch ($join['type']) {
                    case 'left':
                        $sql .= ' LEFT JOIN ';
                        break;
                    case 'right':
                        $sql .= ' RIGHT JOIN ';
                        break;
                    case 'inner':
                    default:
                        $sql .= ' INNER JOIN ';
                }

                foreach ($join['table'] as $alias => $realTable) {
                    $sql .= $this->quote($realTable);

                    if ($realTable != $alias) {
                        $sql .= ' AS ' . $this->quote($alias);
                    }
                    break;
                }

                $sql .= ' ON ' . $join['condition'];
            }
            $sql .= chr(13);
        }
        #</editor-fold>
        #<editor-fold defaultstate="collapsed" desc="Where">
        if (!empty($this->select['where'])) {
            $sql .= ' WHERE ' . $this->select['where'];
            $sql .= chr(13);
        }
        if (!empty($this->select['orWhere'])) {
            if (!empty($this->select['where'])) {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql .= '(';
            foreach ($this->select['orWhere'] as $key => $condition) {
                if (!empty($key)) {
                    $sql .= ' OR ';
                }
                $sql .= $condition;
            }
            $sql .= ')' . chr(13);
        }
        #</editor-fold>
        #<editor-fold defaultstate="collapsed" desc="Group">
        if (!empty($this->select['group'])) {
            $sql .= ' GROUP BY ' . $this->select['group'];
            $sql .= chr(13);
        }
        #</editor-fold>
        #<editor-fold defaultstate="collapsed" desc="Having">
        if (!empty($this->select['having'])) {
            $sql .= ' HAVING ' . $this->select['having'];
            $sql .= chr(13);
        }
        #</editor-fold>
        #<editor-fold defaultstate="collapsed" desc="Order">
        if (!empty($this->select['order'])) {
            $sql .= ' ORDER BY ' . $this->select['order'];
            $sql .= chr(13);
        }
        #</editor-fold>
        #<editor-fold defaultstate="collapsed" desc="Limit">
        if (!empty($this->select['limit']['from']) or ! empty($this->select['limit']['count'])) {
            $sql .= ' LIMIT ' . $this->select['limit']['from'];

            if (!empty($this->select['limit']['count'])) {
                $sql .= ', ' . $this->select['limit']['count'];
            }
        }

        $sql .= chr(13);
        #</editor-fold>

        return $sql;
    }

    private function quote($string)
    {
        $string = '`' . $string . '`';

        return $string;
    }

    private function escapeString($condition, $parameter)
    {
        if (!is_numeric($parameter)) {
            $parameter = $this->parent->escapeString($parameter);
        }

        $condition = str_replace('?', $parameter, $condition);

        return $condition;
    }
}
