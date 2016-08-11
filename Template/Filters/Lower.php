<?php
/**
 * Lower template filter adapter
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

class Lower
{

    public function Get($value)
    {
        $encoding = mb_detect_encoding($value);
        $lower = mb_convert_case($value, MB_CASE_LOWER, $encoding);

        return $lower;
    }
}
