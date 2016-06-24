<?php

/**
 * Sort template filter adapter
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
 * Sort template filter
 * 
 * @package Luki
 */
class Sort
{

    public function Get($value)
    {
        switch ( gettype($value) ) {
            case 'array':
                asort($value);
                $sort = $value;
                break;
            default:
                $sort = $value;
        }

        unset($value);
        return $sort;
    }

}

# End of file