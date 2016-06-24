<?php

/**
 * Capitalize template filter adapter
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
 * Capitalize template filter
 * 
 * @package Luki
 */
class Capitalize
{

    public static function Get($value)
    {
        $value = mb_convert_case($value, MB_CASE_UPPER, 'UTF-8');
        $capitalize = mb_substr($value, 0, 1, 'UTF-8') .
                mb_convert_case(mb_substr($value, 1, mb_strlen($value, 'UTF-8') - 1, 'UTF-8'), MB_CASE_LOWER, 'UTF-8');

        unset($value);
        return $capitalize;
    }

}

# End of file