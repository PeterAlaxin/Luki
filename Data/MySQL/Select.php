<?php

/**
 * MySQL Select class
 *
 * Luki framework
 * Date 8.12.2012
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

/**
 * MySQL Select class
 * 
 * @package Luki
 */
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

    private $_parent = NULL;
    private $_select = array();
    private $_emptySelect = array(
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
        $this->_parent = $parent;

        $this->reset(self::ALL);
    }

    public function __toString()
    {
        $sql = $this->_finalizeSelect();

        return $sql;
    }

    public function distinct()
    {
        $this->_select['distinct'] = 'DISTINCT';

        return $this;
    }

    public function cache()
    {
        $this->_select['cache'] = 'SQL_CACHE';

        return $this;
    }

    public function noCache()
    {
        $this->_select['cache'] = 'SQL_NO_CACHE';

        return $this;
    }

    public function calcFoundRows()
    {
        $this->_select['calc'] = 'SQL_CALC_FOUND_ROWS';

        return $this;
    }

    public function from($table, $columns = '*')
    {
        if ( !is_array($table) ) {
            $table = array( $table => $table );
        }

        $this->_select['from'] = $table;
        $table = $this->_addTable($table);
        $this->_addColumns($table, $columns);

        unset($table, $columns);
        return $this;
    }

    public function join($table, $condition, $columns = '*')
    {
        $this->_join('inner', $table, $condition, $columns);

        unset($table, $condition, $columns);
        return $this;
    }

    public function joinInner($table, $condition, $columns = '*')
    {
        $this->_join('inner', $table, $condition, $columns);

        unset($table, $condition, $columns);
        return $this;
    }

    public function joinLeft($table, $condition, $columns = '*')
    {
        $this->_join('left', $table, $condition, $columns);

        unset($table, $condition, $columns);
        return $this;
    }

    public function joinRight($table, $condition, $columns = '*')
    {
        $this->_join('right', $table, $condition, $columns);

        unset($table, $condition, $columns);
        return $this;
    }

    public function where($condition, $parameter = NULL)
    {
        if ( !empty($this->_select['where']) ) {
            $this->_select['where'] .= ' AND ';
        }

        if ( !is_null($parameter) ) {
            $condition = $this->_escapeString($condition, $parameter);
        }

        $this->_select['where'] .= '(' . $condition . ')';

        unset($condition, $parameter);
        return $this;
    }

    public function orWhere($condition, $parameter = NULL)
    {
        if ( !is_null($parameter) ) {
            $condition = $this->_escapeString($condition, $parameter);
        }

        $this->_select['orWhere'][] = $condition;

        unset($condition, $parameter);
        return $this;
    }

    public function group($groups)
    {
        $groups = (array) $groups;

        foreach ( $groups as $key => $group ) {
            if ( $key > 0 ) {
                $group = ', ' . $group;
            }
            $this->_select['group'] .= $group;
        }

        unset($groups, $group);
        return $this;
    }

    public function having($condition, $parameter = NULL)
    {
        if ( !empty($this->_select['having']) ) {
            $this->_select['having'] .= ' AND ';
        }

        if ( !is_null($parameter) ) {
            $condition = $this->_escape($condition, $parameter);
        }

        $this->_select['having'] .= '(' . $condition . ')';

        unset($condition, $parameter);
        return $this;
    }

    public function orHaving($condition, $parameter = NULL)
    {
        if ( !empty($this->_select['having']) ) {
            $this->_select['having'] = '(' . $this->_select['having'] . ') OR ';
        }

        if ( !is_null($parameter) ) {
            $condition = $this->_escape($condition, $parameter);
        }

        $this->_select['having'] .= '(' . $condition . ')';

        unset($condition, $parameter);
        return $this;
    }

    public function order($orders)
    {
        $orders = (array) $orders;

        foreach ( $orders as $order ) {
            if ( !empty($this->_select['order']) ) {
                $order = ', ' . $order;
            }
            $this->_select['order'] .= $order;
        }

        unset($orders, $order);
        return $this;
    }

    public function limit($from=0, $count = NULL)
    {
        $this->_select['limit']['from'] = (int) $from;

        if ( !is_null($count) ) {
            $this->_select['limit']['count'] = (int) $count;
        }

        unset($from, $count);
        return $this;
    }

    public function page($page, $count)
    {
        $from = ($page - 1) * $count;

        $this->limit($from, $count);

        unset($page, $from, $count);
        return $this;
    }

    public function reset($section)
    {
        if ( isset($this->_emptySelect[$section]) ) {
            $this->_select[$section] = $this->_emptySelect[$section];
        } elseif ( 'all' == $section ) {
            $this->_select = $this->_emptySelect;
        }

        unset($section);
        return $this;
    }

    public function union($sql)
    {

        if ( is_array($sql) ) {
            $this->_select['union'] = $sql;
        }

        unset($sql);
        return $this;
    }

    private function _addTable($table)
    {
        if ( is_array($table) ) {

            foreach ( $table as $key => $value ) {
                $this->_select['tables'][$key] = $value;
                $table = $key;
                break;
            }

            unset($$key, $value);
        } else {
            $this->_select['tables'][$table] = $table;
        }

        return $table;
    }

    private function _addColumns($table, $columns)
    {
        $columns = (array) $columns;

        foreach ( $columns as $alias => $column ) {
            if ( !is_string($alias) ) {
                $alias = $column;
            }

            $this->_select['columns'][$table][$alias] = $column;
        }

        unset($table, $columns, $alias, $column);
    }

    public function _join($type, $table, $condition, $columns)
    {
        if ( !is_array($table) ) {
            $table = array( $table => $table );
        }

        $this->_select['join'][] = array(
          'type' => $type,
          'table' => $table,
          'condition' => $condition
        );

        $table = $this->_addTable($table);
        $this->_addColumns($table, $columns);

        unset($type, $table, $condition, $columns);
    }

    private function _finalizeSelect()
    {
        #<editor-fold defaultstate="collapsed" desc="Union">
        if ( !empty($this->_select['union']) ) {
            $sql = '';

            foreach ( $this->_select['union'] as $key => $select ) {
                if ( $key > 0 ) {
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
            $sql .= $this->_select['distinct'] . ' ';
            $sql .= chr(13);
            #</editor-fold>
            #<editor-fold defaultstate="collapsed" desc="Cache">
            if ( !empty($this->_select['cache']) ) {
                $sql .= $this->_select['cache'] . ' ';
                $sql .= chr(13);
            }
            #</editor-fold>
            #<editor-fold defaultstate="collapsed" desc="Calc rows">
            if ( !empty($this->_select['calc']) ) {
                $sql .= $this->_select['calc'] . ' ';
                $sql .= chr(13);
            }
            #</editor-fold>
            #<editor-fold defaultstate="collapsed" desc="Columns">
            $columns = '';
            foreach ( $this->_select['columns'] as $table => $tableColumns ) {
                $tableColumns = (array) $tableColumns;

                foreach ( $tableColumns as $alias => $column ) {
                    if ( !empty($columns) ) {
                        $columns .= ', ' . chr(13);
                    }

                    if ( FALSE === strstr($column, '(') ) {
                        $columns .= $this->_quote($table) . '.';
                        if ( '*' == $column ) {
                            $columns .= $column;
                        } else {
                            $columns .= $this->_quote($column);
                        }
                    } else {
                        $columns .= $column;
                    }

                    if ( $alias != $column ) {
                        $columns .= ' AS ' . $this->_quote($alias);
                    }
                }
            }
            $sql .= $columns . chr(13);
            #</editor-fold>

            $sql .= ' FROM ';
        }
        #</editor-fold>
        #<editor-fold defaultstate="collapsed" desc="From">
        if ( !empty($this->_select['from']) ) {
            foreach ( $this->_select['from'] as $alias => $realTable ) {
                $sql .= $this->_quote($realTable);

                if ( $realTable != $alias ) {
                    $sql .= ' AS ' . $this->_quote($alias);
                }
                break;
            }
            $sql .= chr(13);
        }
        #</editor-fold>
        #<editor-fold defaultstate="collapsed" desc="Join">
        if ( !empty($this->_select['join']) ) {
            foreach ( $this->_select['join'] as $join ) {

                switch ( $join['type'] ) {
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

                foreach ( $join['table'] as $alias => $realTable ) {
                    $sql .= $this->_quote($realTable);

                    if ( $realTable != $alias ) {
                        $sql .= ' AS ' . $this->_quote($alias);
                    }
                    break;
                }

                $sql .= ' ON ' . $join['condition'];
            }
            $sql .= chr(13);
        }
        #</editor-fold>
        #<editor-fold defaultstate="collapsed" desc="Where">
        if ( !empty($this->_select['where']) ) {
            $sql .= ' WHERE ' . $this->_select['where'];
            $sql .= chr(13);
        }
        if ( !empty($this->_select['orWhere']) ) {
            $sql .= ' AND (';
            foreach ( $this->_select['orWhere'] as $key => $condition ) {
                if ( !empty($key) ) {
                    $sql .= ' OR ';
                }
                $sql .= $condition;
            }
            $sql .= ')' . chr(13);
        }
        #</editor-fold>
        #<editor-fold defaultstate="collapsed" desc="Group">
        if ( !empty($this->_select['group']) ) {
            $sql .= ' GROUP BY ' . $this->_select['group'];
            $sql .= chr(13);
        }
        #</editor-fold>
        #<editor-fold defaultstate="collapsed" desc="Having">
        if ( !empty($this->_select['having']) ) {
            $sql .= ' HAVING ' . $this->_select['having'];
            $sql .= chr(13);
        }
        #</editor-fold>
        #<editor-fold defaultstate="collapsed" desc="Order">
        if ( !empty($this->_select['order']) ) {
            $sql .= ' ORDER BY ' . $this->_select['order'];
            $sql .= chr(13);
        }
        #</editor-fold>
        #<editor-fold defaultstate="collapsed" desc="Limit">
        if(!empty($this->_select['limit']['from']) or !empty($this->_select['limit']['count'])) {
            $sql .= ' LIMIT ' . $this->_select['limit']['from'];

            if ( !empty($this->_select['limit']['count']) ) {
                $sql .= ', ' . $this->_select['limit']['count'];
            }
        }

        $sql .= chr(13);
        #</editor-fold>

        return $sql;
    }

    private function _quote($string)
    {
        $string = '`' . $string . '`';

        return $string;
    }

    private function _escapeString($condition, $parameter)
    {
        if ( !is_numeric($parameter) ) {
            $parameter = $this->_parent->escapeString($parameter);
        }

        $condition = str_replace('?', $parameter, $condition);

        unset($parameter);
        return $condition;
    }

}

# End of file