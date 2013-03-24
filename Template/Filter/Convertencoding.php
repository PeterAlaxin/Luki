<?php

/**
 * Convert encoding template filter adapter
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

/**
 * Convert encoding template filter
 * 
 * @package Luki
 */
class Luki_Template_Filter_Convertencoding {

	protected $aList = array();
	
	public function __construct()
	{
		$this->aList = mb_list_encodings();
	}
	
	public function Get($sValue, $sFrom='UTF-8', $sTo='ISO-8859-1')
	{
		if(in_array($sFrom, $this->aList) and in_array($sTo, $this->aList)) {
			$sReturn = mb_convert_encoding($sValue, $sTo, $sFrom);
		}
		else {
			$sReturn = $sValue;
		}
		
		unset($sValue);
		return $sReturn;
	}
}

# End of file