<?php
/**
 * Cycle template function 
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

class Cycle
{

    public function Get($source, $value)
    {
        if (count($source) <= $value) {
            $ratio = ceil($value / count($source));
            $final = $source;

            for ($i = 1; $i <= $ratio; $i++) {
                $source = array_merge($source, $final);
            }
        }

        $cycle = $source[$value];

        return $cycle;
    }
}
