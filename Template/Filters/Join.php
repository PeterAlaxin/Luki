<?php

/**
 * Join template filter adapter
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
 * Join template filter
 * 
 * @package Luki
 */
class Join
{

    public function Get($value, $separator = '')
    {
        if ( is_array($value) ) {
            $join = implode($separator, $value);
        } else {
            $join = $value;
        }

        unset($value, $separator);
        return $join;
    }

}

# End of file