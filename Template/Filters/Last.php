<?php

/**
 * Last template filter adapter
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

namespace Luki\Template\Filters;

/**
 * Last template filter
 * 
 * @package Luki
 */
class Last
{

    public function Get($value)
    {
        switch ( gettype($value) ) {
            case 'string':
                $last = mb_substr($value, -1);
                break;
            case 'array':
                $value = array_reverse($value);
                $last = array_slice($value, 0, 1);
                break;
            default:
                $last = $value;
        }

        unset($value);
        return $last;
    }

}

# End of file