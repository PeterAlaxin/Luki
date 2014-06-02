<?php

/**
 * Blank template test 
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
 * Blank template test
 * 
 * @package Luki
 */
class Blank
{

    public function Is($value)
    {

        $isBlank = empty($value);

        unset($value);
        return $isBlank;
    }

}

# End of file