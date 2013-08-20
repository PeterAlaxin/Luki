<?php

/**
 * Xml Log Format adapter
 *
 * Luki framework
 * Date 16.12.2012
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

namespace Luki\Log\Format;

use Luki\Log\Format\basicInterface;

/**
 * Xml Log Format
 * 
 * @package Luki
 */
class Xml implements basicInterface {

	public function __construct($sFormat='')
	{
		unset($sFormat);
	}
	
	public function Transform($aParameters)
	{
		$aText = array();
		
		foreach($aParameters as $sKey => $sValue) {
			$aText[$sKey] = $sValue;
		}
		
		unset($aParameters, $sKey, $sValue);
		return $aText;
	}
}

# End of file