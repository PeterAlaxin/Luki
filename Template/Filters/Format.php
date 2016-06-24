<?php

/**
 * Format template filter adapter
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
 * Format template filter
 * 
 * @package Luki
 */
class Format
{

    public function Get($value, $par1 = NULL, $par2 = NULL, $par3 = NULL, $par4 = NULL, $par5 = NULL, $par6 = NULL, $par7 = NULL, $par8 = NULL, $par9 = NULL, $par10 = NULL)
    {
        $function = '$format = sprintf($value';

        for ( $i = 1; $i < 11; $i++ ) {
            eval('$par = is_null($par' . $i . ');');

            if ( !$par ) {
                $function .= ', $par' . $i;
            }
        }

        $function .= ');';

        eval($function);

        unset($value, $par1, $par2, $par3, $par4, $par5, $par6, $par7, $par8, $par9, $par10);
        return $format;
    }

}

# End of file