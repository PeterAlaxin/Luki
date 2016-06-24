<?php

/**
 * First template filter adapter
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
 * First template filter
 * 
 * @package Luki
 */
class First
{

    public function Get($value)
    {
        switch ( gettype($value) ) {
            case 'string':
                $first = mb_substr($value, 0, 1);
                break;
            case 'array':
                $first = array_slice($value, 0, 1);
                break;
            default:
                $first = $value;
        }

        unset($value);
        return $first;
    }

}

# End of file