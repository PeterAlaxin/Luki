<?php

/**
 * Preset template filter adapter
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
 * Preset template filter
 * 
 * @package Luki
 */
class Preset
{

    public function Get($value, $default = '')
    {
        $preset = empty($value) ? $default : $value;

        unset($value, $default);
        return $preset;
    }

}

# End of file