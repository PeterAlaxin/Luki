<?php

/**
 * Random template function 
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

namespace Luki\Template\Functions;

/**
 * Random template function
 * 
 * @package Luki
 */
class Random {

	public function Get($sValue = NULL)
	{
		switch(gettype($sValue)) {
			case 'string':
				$sReturn = substr($sValue, mt_rand(0, strlen($sValue)-1), 1);
				break;
			case 'array':
				$sReturn = $sValue[mt_rand(0, count($sValue)-1)];
				break;
			case 'integer':
				$sReturn = mt_rand(0, $sValue);
				break;
			case 'NULL':
			default :
				$sReturn = mt_rand();
		}
		
		unset($sValue);
		return $sReturn;
	}
}

# End of file