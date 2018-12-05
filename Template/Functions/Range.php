<?php
/**
 * Range template function
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

namespace Luki\Template\Functions;

class Range
{

    public function Get($begin, $end, $step = 1)
    {
        $range = range($begin, $end, $step);

        return $range;
    }
}