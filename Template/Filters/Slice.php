<?php

/**
 * Slice template filter adapter
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
 * Slice template filter
 * 
 * @package Luki
 */
class Slice
{

    public function Get($value, $start = 0, $length = 1)
    {
        switch ( gettype($value) ) {
            case 'string':
                $encoding = mb_detect_encoding($value);
                $slice = mb_substr($value, $start, $length, $encoding);
                break;
            case 'array':
                $slice = array_slice($value, $start, $length);
                break;
            default:
                $slice = $value;
        }

        unset($value, $start, $length, $encoding);
        return $slice;
    }

}

# End of file