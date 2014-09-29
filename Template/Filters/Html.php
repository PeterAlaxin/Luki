<?php

/**
 * Html template filter adapter
 *
 * Luki framework
 * Date 24.8.2013
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
 * Html template filter
 * 
 * @package Luki
 */
class Html
{

    public function Get($value)
    {
        $html = html_entity_decode($value);

        unset($value);
        return $html;
    }

}

# End of file