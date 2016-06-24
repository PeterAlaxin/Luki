<?php

/**
 * Keys template filter adapter
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
 * Keys template filter
 * 
 * @package Luki
 */
class Keys
{

    public function Get($value)
    {
        if ( is_array($value) ) {
            $keys = array_keys($value);
        } else {
            $keys = $value;
        }

        unset($value);
        return $keys;
    }

}

# End of file