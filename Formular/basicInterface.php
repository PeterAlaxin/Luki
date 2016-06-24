<?php

/**
 * Formular input interface
 *
 * Luki framework
 * Date 30.5.2014
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Formular
 * @filesource
 */

namespace Luki\Formular;

/**
 * Formular input interface
 * 
 * @package Luki
 */
interface basicInterface
{

    public function __construct($name, $label, $placeholder);
}

# End of file