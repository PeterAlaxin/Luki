<?php
/**
 * Capitalize template filter adapter
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Template
 * @filesource
 */

namespace Luki\Template\Filters;

class Capitalize
{

    public static function Get($value)
    {
        $value = mb_convert_case($value, MB_CASE_UPPER, 'UTF-8');
        $capitalize = mb_substr($value, 0, 1, 'UTF-8') .
            mb_convert_case(mb_substr($value, 1, mb_strlen($value, 'UTF-8') - 1, 'UTF-8'), MB_CASE_LOWER, 'UTF-8');

        return $capitalize;
    }
}
