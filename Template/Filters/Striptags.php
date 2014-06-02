<?php

/**
 * Striptags template filter adapter
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

namespace Luki\Template\Filters;

/**
 * Striptags template filter
 * 
 * @package Luki
 */
class Striptags
{

    public function Get($value)
    {
        $stripped = strip_tags($value);

        unset($value);
        return $stripped;
    }

}

# End of file