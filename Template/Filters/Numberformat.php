<?php

/**
 * Numberformat template filter adapter
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
 * Numberformat template filter
 * 
 * @package Luki
 */
class Numberformat
{

    public function Get($value, $decimal = 2, $delimiterDecimals = ',', $delimiterThousands = '.')
    {
        $number = number_format($value, $decimal, $delimiterDecimals, $delimiterThousands);

        unset($value);
        return $number;
    }

}

# End of file