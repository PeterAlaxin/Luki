<?php

/**
 * Merge template filter adapter
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
 * Merge template filter
 * 
 * @package Luki
 */
class Merge
{

    public function Get($value, $mergeWith)
    {
        if ( is_array($value) and is_array($mergeWith) ) {
            $merge = array_merge($value, $mergeWith);
        } else {
            $merge = $value;
        }

        unset($value, $mergeWith);
        return $merge;
    }

}

# End of file