<?php

/**
 * Navigation Format interface
 *
 * Luki framework
 * Date 17.12.2012
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

namespace Luki\Navigation\Format;

/**
 * Navigation Format interface
 * 
 * @package Luki
 */
interface basicInterface {

	public function setFormat($sFormat);

	public function Format($aOptions);
}

# End of file