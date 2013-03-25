<?php

/**
 * Odd template test 
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
 * Odd template test
 * 
 * @package Luki
 */
class Luki_Template_Test_Odd {

	public function Is($nValue)
	{
		
		$bReturn = !(floor($nValue/2) == ($nValue/2));
		
		unset($nValue);
		return $bReturn;
	}
}

# End of file