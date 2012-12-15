<?php

/**
 * Validator interface
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
 * Validator interface
 * 
 * @package Luki
 */
interface Luki_Validator_Interface {

	public function __construct($aOptions);
	
	public function isValid($xValue);

	public function setMessage($sMessage);

	public function getError();
}

# End of file