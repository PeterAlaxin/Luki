<?php

/**
 * Cycle template function 
 *
 * Luki framework
 * Date 22.3.2013
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki\Template\Functions;

/**
 * Cycle template function
 * 
 * @package Luki
 */
class Cycle
{

    public function Get($source, $value)
    {
        if ( count($source) <= $value ) {
            $ratio = ceil($value / count($source));
            $final = $source;

            for ( $i = 1; $i <= $ratio; $i++ ) {
                $source = array_merge($source, $final);
            }
        }

        $cycle = $source[$value];

        unset($source, $value, $final, $i, $ratio);
        return $cycle;
    }

}

# End of file