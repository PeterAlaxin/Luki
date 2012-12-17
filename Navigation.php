<?php

/**
 * Navigation class
 *
 * Luki framework
 * Date 17.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

/**
 * Navigation class
 *
 * @package Luki
 */
class Luki_Navigation {

	private $aNavigation = array();

	public function addItem($oItem)
	{
		$nParent = $oItem->parent;
		
		if(0 == $nParent) {
			$this->aNavigation[] = $oItem;
		}
		else {
			$oFoundItem = $this->getItem($nParent);

			if(!empty($oFoundItem)) {
				$oFoundItem->addItem($oItem);
			}
		}
		
		return $this;
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