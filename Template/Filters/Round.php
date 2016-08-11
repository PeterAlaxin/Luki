<?php
/**
 * Round template filter adapter
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

class Round
{

    public function Get($value, $decimals = 0, $direction = '')
    {
        if ('ceil' == $direction) {
            $number = ceil($value * pow(10, $decimals)) / pow(10, $decimals);
        } elseif ('floor' == $direction) {
            $number = floor($value * pow(10, $decimals)) / pow(10, $decimals);
        } else {
            $number = round($value, $decimals);
        }

        return $number;
    }
}
