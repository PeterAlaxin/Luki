<?php

/**
 * Log Format interface
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

/**
 * Log Format interface
 * 
 * @package Luki
 */
interface basicInterface {

	public function __construct($sFormat);

	public function Transform($aParameters);
}

# End of file