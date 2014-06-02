<?php

/**
 * Divisibleby template test 
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

namespace Luki\Template\Tests;

/**
 * Divisibleby template test
 * 
 * @package Luki
 */
class Divisibleby
{

    public function Is($value, $divider)
    {

        $isDivisible = (floor($value / $divider) == ($value / $divider));

        unset($value, $divider);
        return $isDivisible;
    }

}

# End of file