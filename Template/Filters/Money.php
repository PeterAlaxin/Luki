<?php

/**
 * Money template filter adapter
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
 * Money template filter
 * 
 * @package Luki
 */
class Money
{

    public function Get($value, $format = NULL)
    {
        if ( !$this->_isWindows() ) {
            if ( empty($format) ) {
                $money = money_format('%!n', (float) $value);
            } elseif ( 'eur' == $format ) {
                $money = money_format('%!n&nbsp;€', (float) $value);
            } else {
                $money = money_format($format, (float) $value);
            }
        } else {
            $money = number_format((float) $value, 2, ',', '.');

            if ( 'eur' == $format ) {
                $money .= '&nbsp;€';
            }
        }

        unset($value, $format);
        return $money;
    }

    private function _isWindows()
    {
        $isWindows = FALSE;
        
        if(!empty($_SERVER["HTTP_USER_AGENT"])) {
            if(0 === preg_match('/windows/i', $_SERVER["HTTP_USER_AGENT"])) {
                $isWindows = TRUE;
            }
        }
        
        return $isWindows;
    }
}

# End of file