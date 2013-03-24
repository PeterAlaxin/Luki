<?php

/**
 * Escape template filter adapter
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
 * Escape template filter
 * 
 * @package Luki
 */
class Luki_Template_Filter_Escape {

	public function Get($sValue, $sStrategy = 'html', $sCharset = 'UTF-8')
	{
		switch($sStrategy) {
			case 'js':
				$sReturn = htmlspecialchars($sValue, ENT_QUOTES, $sCharset);
				$sReturn = json_encode($sReturn);
				break;
			case 'url':
				$sReturn = urlencode($sValue);
				break;
			case 'html':
			default:
				$sReturn = htmlspecialchars($sValue, ENT_QUOTES, $sCharset);
		}
		
		unset($sValue, $sStrategy, $sCharset);
		return $sReturn;
	}
}

# End of file