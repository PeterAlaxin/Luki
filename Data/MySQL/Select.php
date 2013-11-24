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
class Select {

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

	private $oParent = NULL;

	/**
	 * All selectes
	 * @var array 
	 */
	private $aSelect = array();

	/**
	 * Empty select array
	 * @var array
	 */
	private $aEmptySelect = array(
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

	public function __construct($oParent)
	{
		$this->oParent = $oParent;

		$this->reset(self::ALL);
	}

	public function __toString()
	{
		$sReturn = $this->_finalizeSelect();

		return $sReturn;
	}

	public function distinct()
	{
		$this->aSelect['distinct'] = 'DISTINCT';

		return $this;
	}

	public function cache()
	{
		$this->aSelect['cache'] = 'SQL_CACHE';

		return $this;
	}

	public function noCache()
	{
		$this->aSelect['cache'] = 'SQL_NO_CACHE';

		return $this;
	}

	public function calcFoundRows()
	{
		$this->aSelect['calc'] = 'SQL_CALC_FOUND_ROWS';

		return $this;
	}

	public function from($sTable, $aColumns = '*')
	{
		if(!is_array($sTable)) {
			$sTable = array($sTable => $sTable);
		}

		$this->aSelect['from'] = $sTable;
		$sTable = $this->_addTable($sTable);
		$this->_addColumns($sTable, $aColumns);

		unset($sTable, $aColumns);
		return $this;
	}

	public function join($sTable, $sCondition, $aColumns = '*')
	{
		$this->_join('inner', $sTable, $sCondition, $aColumns);

		unset($sTable, $sCondition, $aColumns);
		return $this;
	}

	public function joinInner($sTable, $sCondition, $aColumns = '*')
	{
		$this->_join('inner', $sTable, $sCondition, $aColumns);

		unset($sTable, $sCondition, $aColumns);
		return $this;
	}

	public function joinLeft($sTable, $sCondition, $aColumns = '*')
	{
		$this->_join('left', $sTable, $sCondition, $aColumns);

		unset($sTable, $sCondition, $aColumns);
		return $this;
	}

	public function joinRight($sTable, $sCondition, $aColumns = '*')
	{
		$this->_join('right', $sTable, $sCondition, $aColumns);

		unset($sTable, $sCondition, $aColumns);
		return $this;
	}

	public function where($sCondition, $sParameter = NULL)
	{
		if(!empty($this->aSelect['where'])) {
			$this->aSelect['where'] .= ' AND ';
		}

		if(!is_null($sParameter)) {
			$sCondition = $this->_escapeString($sCondition, $sParameter);
		}

		$this->aSelect['where'] .= '(' . $sCondition . ')';

		unset($sCondition, $sParameter);
		return $this;
	}

	public function orWhere($sCondition, $sParameter = NULL)
	{
		if(!is_null($sParameter)) {
			$sCondition = $this->_escapeString($sCondition, $sParameter);
		}

		$this->aSelect['orWhere'][] = $sCondition;

		unset($sCondition, $sParameter);
		return $this;
	}

	public function group($aGroup)
	{
		$aGroup = (array) $aGroup;

		foreach ($aGroup as $nKey => $sGroup) {
			if($nKey > 0) {
				$sGroup = ', ' . $sGroup;
			}
			$this->aSelect['group'] .= $sGroup;
		}

		unset($aGroup, $sGroup);
		return $this;
	}

	public function having($sCondition, $sParameter = NULL)
	{
		if(!empty($this->aSelect['having'])) {
			$this->aSelect['having'] .= ' AND ';
		}

		if(!is_null($sParameter)) {
			$sCondition = $this->_escape($sCondition, $sParameter);
		}

		$this->aSelect['having'] .= '(' . $sCondition . ')';

		unset($sCondition, $sParameter);
		return $this;
	}

	public function orHaving($sCondition, $sParameter = NULL)
	{
		if(!empty($this->aSelect['having'])) {
			$this->aSelect['having'] = '(' . $this->aSelect['having'] . ') OR ';
		}

		if(!is_null($sParameter)) {
			$sCondition = $this->_escape($sCondition, $sParameter);
		}

		$this->aSelect['having'] .= '(' . $sCondition . ')';

		unset($sCondition, $sParameter);
		return $this;
	}

	public function order($aOrder)
	{
		$aOrder = (array) $aOrder;

		foreach ($aOrder as $sOrder) {
			if(!empty($this->aSelect['order'])) {
				$sOrder = ', ' . $sOrder;
			}
			$this->aSelect['order'] .= $sOrder;
		}

		unset($aOrder, $sOrder);
		return $this;
	}

	public function limit($nFrom, $nCount = NULL)
	{
		$this->aSelect['limit']['from'] = (int) $nFrom;

		if(!is_null($nCount)) {
			$this->aSelect['limit']['count'] = (int) $nCount;
		}

		unset($nFrom, $nCount);
		return $this;
	}

	public function page($nPage, $nCount)
	{
		$nFrom = ($nPage - 1) * $nCount;

		$this->limit($nFrom, $nCount);

		unset($nPage, $nFrom, $nCount);
		return $this;
	}

	public function reset($sSection)
	{
		if(isset($this->aEmptySelect[$sSection])) {
			$this->aSelect[$sSection] = $this->aEmptySelect[$sSection];
		}
		elseif('all' == $sSection) {
			$this->aSelect = $this->aEmptySelect;
		}

		unset($sSection);
		return $this;
	}

	public function union($aSQL)
	{

		if(is_array($aSQL)) {
			$this->aSelect['union'] = $aSQL;
		}

		unset($aSQL);
		return $this;
	}

	private function _addTable($sTable)
	{
		if(is_array($sTable)) {

			foreach ($sTable as $sKey => $sValue) {
				$this->aSelect['tables'][$sKey] = $sValue;
				$sTable = $sKey;
				break;
			}

			unset($$sKey, $sValue);
		}
		else {
			$this->aSelect['tables'][$sTable] = $sTable;
		}

		return $sTable;
	}

	private function _addColumns($sTable, $aColumns)
	{
		$aColumns = (array) $aColumns;

		foreach ($aColumns as $sAlias => $sColumn) {
			if(!is_string($sAlias)) {
				$sAlias = $sColumn;
			}

			$this->aSelect['columns'][$sTable][$sAlias] = $sColumn;
		}

		unset($sTable, $aColumns, $sAlias, $sColumn);
	}

	public function _join($sType, $sTable, $sCondition, $aColumns)
	{
		if(!is_array($sTable)) {
			$sTable = array($sTable => $sTable);
		}

		$this->aSelect['join'][] = array(
			'type' => $sType,
			'table' => $sTable,
			'condition' => $sCondition
		);

		$sTable = $this->_addTable($sTable);
		$this->_addColumns($sTable, $aColumns);

		unset($sType, $sTable, $sCondition, $aColumns);
	}

	private function _finalizeSelect()
	{
		#<editor-fold defaultstate="collapsed" desc="Union">
		if(!empty($this->aSelect['union'])) {
			$sSQL = '';

			foreach ($this->aSelect['union'] as $nKey => $oSelect) {
				if($nKey > 0) {
					$sSQL .= ' UNION ' . chr(13);
				}
				$sSQL .= ' (' . (string) $oSelect . ')' . chr(13);
			}
		}
		#</editor-fold>
		#<editor-fold defaultstate="collapsed" desc="Single Select">
		else {
			$sSQL = 'SELECT ';

			#<editor-fold defaultstate="collapsed" desc="Distinct">
			$sSQL .= $this->aSelect['distinct'] . ' ';
			$sSQL .= chr(13);
			#</editor-fold>
			#<editor-fold defaultstate="collapsed" desc="Cache">
			if(!empty($this->aSelect['cache'])) {
				$sSQL .= $this->aSelect['cache'] . ' ';
				$sSQL .= chr(13);
			}
			#</editor-fold>
			#<editor-fold defaultstate="collapsed" desc="Calc rows">
			if(!empty($this->aSelect['calc'])) {
				$sSQL .= $this->aSelect['calc'] . ' ';
				$sSQL .= chr(13);
			}
			#</editor-fold>
			#<editor-fold defaultstate="collapsed" desc="Columns">
			$sColumns = '';
			foreach ($this->aSelect['columns'] as $sTable => $aColumns) {
				$aColumns = (array) $aColumns;

				foreach ($aColumns as $sAlias => $sColumn) {
					if(!empty($sColumns)) {
						$sColumns .= ', ' . chr(13);
					}

					if(FALSE === strstr($sColumn, '(')) {
						$sColumns .= $this->_quote($sTable) . '.';
						if('*' == $sColumn) {
							$sColumns .= $sColumn;
						}
						else {
							$sColumns .= $this->_quote($sColumn);
						}
					}
					else {
						$sColumns .= $sColumn;
					}

					if($sAlias != $sColumn) {
						$sColumns .= ' AS ' . $this->_quote($sAlias);
					}
				}
			}
			$sSQL .= $sColumns . chr(13);
			#</editor-fold>

			$sSQL .= ' FROM ';
		}
		#</editor-fold>
		#<editor-fold defaultstate="collapsed" desc="From">
		if(!empty($this->aSelect['from'])) {
			foreach ($this->aSelect['from'] as $sAlias => $sRealTable) {
				$sSQL .= $this->_quote($sRealTable);

				if($sRealTable != $sAlias) {
					$sSQL .= ' AS ' . $this->_quote($sAlias);
				}
				break;
			}
			$sSQL .= chr(13);
		}
		#</editor-fold>
		#<editor-fold defaultstate="collapsed" desc="Join">
		if(!empty($this->aSelect['join'])) {
			foreach ($this->aSelect['join'] as $aJoin) {

				switch ($aJoin['type']) {
					case 'left':
						$sSQL .= ' LEFT JOIN ';
						break;
					case 'right':
						$sSQL .= ' RIGHT JOIN ';
						break;
					case 'inner':
					default:
						$sSQL .= ' INNER JOIN ';
				}

				foreach ($aJoin['table'] as $sAlias => $sRealTable) {
					$sSQL .= $this->_quote($sRealTable);

					if($sRealTable != $sAlias) {
						$sSQL .= ' AS ' . $this->_quote($sAlias);
					}
					break;
				}

				$sSQL .= ' ON ' . $aJoin['condition'];
			}
			$sSQL .= chr(13);
		}
		#</editor-fold>
		#<editor-fold defaultstate="collapsed" desc="Where">
		if(!empty($this->aSelect['where'])) {
			$sSQL .= ' WHERE ' . $this->aSelect['where'];
			$sSQL .= chr(13);
		}
		if(!empty($this->aSelect['orWhere'])) {
			$sSQL .= ' AND (';
            foreach($this->aSelect['orWhere'] as $Key => $Condition) {
                if(!empty($Key)) {
                    $sSQL .= ' OR ';
                }
                $sSQL .= $Condition;
            }
			$sSQL .= ')'. chr(13);
		}
		#</editor-fold>
		#<editor-fold defaultstate="collapsed" desc="Group">
		if(!empty($this->aSelect['group'])) {
			$sSQL .= ' GROUP BY ' . $this->aSelect['group'];
			$sSQL .= chr(13);
		}
		#</editor-fold>
		#<editor-fold defaultstate="collapsed" desc="Having">
		if(!empty($this->aSelect['having'])) {
			$sSQL .= ' HAVING ' . $this->aSelect['having'];
			$sSQL .= chr(13);
		}
		#</editor-fold>
		#<editor-fold defaultstate="collapsed" desc="Order">
		if(!empty($this->aSelect['order'])) {
			$sSQL .= ' ORDER BY ' . $this->aSelect['order'];
			$sSQL .= chr(13);
		}
		#</editor-fold>
		#<editor-fold defaultstate="collapsed" desc="Limit">
		if(!empty($this->aSelect['limit']['from'])) {
			$sSQL .= ' LIMIT ' . $this->aSelect['limit']['from'];

			if(!empty($this->aSelect['limit']['count'])) {
				$sSQL .= ', ' . $this->aSelect['limit']['count'];
			}

			$sSQL .= chr(13);
		}
		#</editor-fold>

		return $sSQL;
	}

	private function _quote($sString)
	{
		$sString = '`' . $sString . '`';

		return $sString;
	}

	private function _escapeString($sCondition, $sParameter)
	{
		if(!is_numeric($sParameter)) {
			$sParameter = $this->oParent->escapeString($sParameter);
		}

		$sCondition = str_replace('?', $sParameter, $sCondition);

		unset($sParameter);
		return $sCondition;
	}

}

# End of file