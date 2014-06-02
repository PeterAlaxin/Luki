<?php

/**
 * Defined template test 
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
 * Defined template test
 * 
 * @package Luki
 */
class Defined
{

    public function Is($value)
    {

        $isDefined = !is_null($value);

        unset($value);
        return $isDefined;
    }

}

# End of file