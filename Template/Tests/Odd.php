<?php

/**
 * Odd template test 
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
 * Odd template test
 * 
 * @package Luki
 */
class Odd
{

    public function Is($value)
    {

        $isOdd = !(floor($value / 2) == ($value / 2));

        unset($value);
        return $isOdd;
    }

}

# End of file