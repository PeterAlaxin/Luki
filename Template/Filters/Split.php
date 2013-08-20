<?php

/**
 * Split template filter adapter
 *
 * Luki framework
 * Date 22.3.2013
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

namespace Luki\Template\Filters;

/**
 * Split template filter
 * 
 * @package Luki
 */
class Split {

	public function Get($sValue, $sSeparator = '', $nLimit = 0)
	{
		switch(gettype($sValue)) {
			case 'string':
				if(empty($sSeparator)) {
					$aValue = preg_split("//u", $sValue, NULL, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
					
					if($nLimit > 0) {
						$aNewValue = array();
						$nCount = 0;
						$sItem = ''; 

						foreach($aValue as $sValue) {
							$sItem .= $sValue;
							$nCount++;
						
							if($nCount == $nLimit) {
								$nCount = 0;
								$aNewValue[] = $sItem;
								$sItem = '';
							}
						}
						
						if(!empty($sItem)) {
							$aNewValue[] = $sItem;							
						}
						
						$aValue = $aNewValue;
					}
				}
				else {
					$aValue = preg_split("/" . $sSeparator . "/u", $sValue, $nLimit, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
				}

				$sReturn = $aValue;
				break;
			default:
				$sReturn = $sValue;
		}
		
		unset($sValue, $aValue);
		return $sReturn;
	}
}

# End of file