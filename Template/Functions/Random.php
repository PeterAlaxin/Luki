<?php

/**
 * Random template function 
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

namespace Luki\Template\Functions;

/**
 * Random template function
 * 
 * @package Luki
 */
class Random
{

    public function Get($value = NULL)
    {
        switch ( gettype($value) ) {
            case 'string':
                $random = substr($value, mt_rand(0, strlen($value) - 1), 1);
                break;
            case 'array':
                $random = $value[mt_rand(0, count($value) - 1)];
                break;
            case 'integer':
                $random = mt_rand(0, $value);
                break;
            case 'NULL':
            default :
                $random = mt_rand();
        }

        unset($value);
        return $random;
    }

}

# End of file