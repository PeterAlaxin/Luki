<?php

/**
 * Crumb Navigation Format
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

namespace Luki\Navigation\Format;

use Luki\Navigation\Format\basicInterface;

/**
 * Crumb Navigation Format
 * 
 * @package Luki
 */
class Crumb implements basicInterface {

	private $sFormat = '<a href="%url%" title="%title%" class="%class%" target="%target%">%label%</a>';
	private $oNavigation = NULL;	
	private $aUsed = array(
		'label',
		'title',
		'class',
		'target'
		);
		
	public function __construct($oNavigation)
	{
		$this->oNavigation = $oNavigation;
		
		unset($oNavigation);
	}
	
	public function setFormat($sFormat) {
		$this->sFormat = $sFormat;
		
		unset($sFormat);
		return $this;
	}
	
	public function Format($aOptions)
	{
		$nItem = $aOptions['id'];
		$aItems = $this->_createArray($nItem);
		$sCrumb = '';
		$sReturn = '';
		
		foreach($aItems as $oItem) {
			$sFormat = $this->sFormat;
			
			foreach($this->aUsed as $sKey) {
				$sFormat = preg_replace('/%' . $sKey . '%/', $oItem->$sKey, $sFormat);
			}
			
			$sCrumb .= $oItem->crumb . '/'; 
			$sReturn .= preg_replace('/%url%/', $sCrumb, $sFormat);
		}
		
		unset($aOptions, $nItem, $oItem, $sKey, $sCrumb, $aItems, $sFormat);
		return $sReturn;
	}
	
	private function _createArray($nItem)
	{
		$aItems = array();
		
		do {
			$oItem = $this->oNavigation->getItem($nItem);
			$aItems[] = $oItem;
			$nItem = $oItem->parent;
		}
		while ($nItem > 0);

		$aReturn = array_reverse($aItems);
		
		unset($nItem, $oItem, $aItems);
		return $aReturn;
	}
}

# End of file