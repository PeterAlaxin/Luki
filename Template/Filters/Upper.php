<?php
/**
 * Upper template filter adapter
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

class Upper
{

    public function Get($value)
    {
        $encoding = mb_detect_encoding($value);
        $upper    = mb_convert_case($value, MB_CASE_UPPER, $encoding);

        return $upper;
    }
}