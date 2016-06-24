<?php

/**
 * Trim template filter adapter
 *
 * Luki framework
 * Date 22.3.2013
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki\Template\Filters;

/**
 * Trim template filter
 * 
 * @package Luki
 */
class Trim
{

    public function Get($value, $charList = '')
    {
        $trimmed = trim($value, ' \t\n\r\0\x0B' . $charList);

        unset($value, $charList);
        return $trimmed;
    }

}

# End of file