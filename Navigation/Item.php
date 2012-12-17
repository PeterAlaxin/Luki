<?php

/**
 * Navigation Item
 *
 * Luki framework
 * Date 17.12.2012
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

/**
 * Navigation Item
 * 
 * @package Luki
 */
class Luki_Navigation_Item {

	private $aItem = array(
		'id' => 0,
		'parent' => 0,
		'label' => '',
		'crumb' => '',
		'title' => '',
		'url' => '',
		'class' => '',
		'visible' => TRUE,
		'active' => FALSE,
		'controller' => '',
		'action' => '',
	);
	private $aNavigation = array();

	/**
	 * Constructor
	 * @param int $nID
	 */
	public function __construct($nID)
	{
		$this->aItem['id'] = (int) $nID;

		unset($nID);
	}

	public function __call($sMethod, $aParam)
	{
		if(!empty($aParam[0])) {
			$this->aItem[$sMethod] = $aParam[0];
		}

		if('label' == $sMethod) {
			$this->aItem['crumb'] = Luki_Url::makeLink($aParam[0]);
		}

		unset($sMethod, $aParam);
		return $this;
	}

	public function __get($sName)
	{
		$xValue = NULL;

		if(isset($this->aItem[$sName])) {
			$xValue = $this->aItem[$sName];
		}

		unset($sName);
		return $xValue;
	}

	public function addItem($oItem)
	{
		$this->aNavigation[] = $oItem;
	}

	public function getItem($nID)
	{
		$oFoundItem = NULL;

		foreach ($this->aNavigation as $oItem) {
			if($nID == $oItem->id) {
				$oFoundItem = $oItem;
				break;
			}
			else {
				$oFoundItem = $oItem->getItem($nID);

				if(!empty($oFoundItem)) {
					break;
				}
			}
		}

		unset($nID, $oItem);
		return $oFoundItem;
	}

}

# End of file