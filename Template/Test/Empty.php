<?php

/**
 * Empty template test 
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
 * Empty template test
 * 
 * @package Luki
 */
class Luki_Template_Test_Empty {

	public function Is($sValue)
	{
		
		$bReturn = empty($sValue);
		
		unset($sValue);
		return $bReturn;
	}
}

# End of file