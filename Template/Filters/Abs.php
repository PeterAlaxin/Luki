<?php

/**
 * Abs template filter adapter
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

namespace Luki\Template\Filters;

/**
 * Abs template filter
 * 
 * @package Luki
 */
class Abs
{

    public function Get($value)
    {
        $abs = abs($value);

        unset($value);
        return $abs;
    }

}

# End of file