<?php

/**
 * Digit validator
 *
 * Luki framework
 * Date 14.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Validator
 * @filesource
 */

/**
 * Digit validator
 * 
 * @package Luki
 */
class Luki_Validator_Digit extends Luki_Validator_Factory {

	public $sValidator = '/^[0-9]*$/';
	
	public $sMessage = 'The value "%value%" contains characters other than numbers!';

}

# End of file