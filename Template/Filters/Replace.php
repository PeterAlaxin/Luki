<?php

/**
 * Replace template filter adapter
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
 * Replace template filter
 * 
 * @package Luki
 */
class Replace
{

    public function Get($value, $parameters)
    {
        foreach ( $parameters as $from => $to ) {
            $value = str_replace($from, $to, $value);
        }

        unset($parameters, $from, $to);
        return $value;
    }

}

# End of file