<?php

/**
 * Range template function 
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

namespace Luki\Template\Functions;

/**
 * Range template function
 * 
 * @package Luki
 */
class Range
{

    public function Get($begin, $end, $step = 1)
    {
        $range = range($begin, $end, $step);

        unset($begin, $end, $step);
        return $range;
    }

}

# End of file