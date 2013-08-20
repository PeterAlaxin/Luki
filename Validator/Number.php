<?php

/**
 * Number validator
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

namespace Luki\Validator;

use Luki\Validator\basicFactory;

/**
 * Number validator
 * 
 * @package Luki
 */
class Number extends basicFactory {

	public $sValidator = '/^[-]?[0-9]*[.]?[0-9]*$/';
	
	public $sMessage = 'The value "%value%" is not valid number!';

}

# End of file