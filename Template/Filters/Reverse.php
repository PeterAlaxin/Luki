<?php

/**
 * Reverse template filter adapter
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
 * Reverse template filter
 * 
 * @package Luki
 */
class Reverse
{

    public function Get($value)
    {
        switch ( gettype($value) ) {
            case 'string':
                $value = preg_split("//u", $value, NULL, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                $reverse = implode('', array_reverse($value));
                break;
            case 'array':
                $reverse = array_reverse($value);
                break;
            default:
                $reverse = $value;
        }

        unset($value);
        return $reverse;
    }

}

# End of file