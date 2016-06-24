<?php

/**
 * Bites template filter adapter
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
 * Bites template filter
 * 
 * @package Luki
 */
class Bites
{

    public function Get($value)
    {
        if ( $value < 1024 ) {
            $bites = number_format($value, 0) . '&nbsp;B';
        } elseif ( $value < 1048576 ) {
            $bites = number_format($value / 1024, 2, ',', '.') . '&nbsp;kB';
        } elseif ( $value < 1073741824 ) {
            $bites = number_format($value / 1048576, 2, ',', '.') . '&nbsp;MB';
        } else {
            $bites = number_format($value / 1073741824, 2, ',', '.') . '&nbsp;GB';
        }

        unset($value);
        return $bites;
    }

}

# End of file