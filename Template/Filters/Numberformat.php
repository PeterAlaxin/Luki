<?php
/**
 * Numberformat template filter adapter
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

class Numberformat
{

    public function Get($value, $decimal = 2, $delimiterDecimals = ',', $delimiterThousands = '.')
    {
        $number = number_format($value, $decimal, $delimiterDecimals, $delimiterThousands);

        return $number;
    }
}
