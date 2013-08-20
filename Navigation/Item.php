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

namespace Luki\Navigation;

use Luki\Url;

/**
 * Navigation Item
 * 
 * @package Luki
 */
class Item {

	private $aItem = array(
		'id' => 0,
		'parent' => 0,
		'label' => '',
		'crumb' => '',
		'title' => '',
		'url' => '',
		'target' => '_self',
		'class' => '',
		'hidden' => '',
		'active' => '',
		'controller' => '',
		'action' => '',
	);
	private $aNavigation = array();

	/**
	 * Constructor
	 * @param int $nID
	 */
	public function __construct($nID, $sLabel='', $sUrl = '')
	{
		$this->aItem['id'] = (int) $nID;
        
        if(!empty($sLabel)) {
            $this->label($sLabel);
        }

        if(!empty($sUrl)) {
            $this->crumb($sUrl);
        }

		unset($nID, $sLabel, $sUrl);
	}

	public function __call($sMethod, $aParam)
	{
		if(!empty($aParam[0]) or in_array($aParam[0], array(0, FALSE, '')) ) {
			$this->aItem[$sMethod] = $aParam[0];
		}

		if('label' == $sMethod) {
			$this->aItem['crumb'] = Url::makeLink($aParam[0]);
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

	public function getNavigation()
	{
		return $this->aNavigation;
	}

}

# End of file