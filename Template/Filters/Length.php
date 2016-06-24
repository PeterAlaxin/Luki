<?php

/**
 * Length template filter adapter
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
 * Length template filter
 * 
 * @package Luki
 */
class Length
{

    public function Get($value)
    {
        switch ( gettype($value) ) {
            case 'string':
                $length = strlen($value);
                break;
            case 'array':
                $length = count($value);
                break;
            case 'object':
                $length = 0;
                if(is_a($value, 'Iterator')) {
                    $length = iterator_count($value);
                }
                break;
            default:
                $length = $value;
        }

        unset($value);
        return $length;
    }

}

# End of file