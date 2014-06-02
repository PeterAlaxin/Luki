<?php

/**
 * Escape template filter adapter
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
 * Escape template filter
 * 
 * @package Luki
 */
class Escape
{

    public function Get($value, $strategy = 'html', $charset = 'UTF-8')
    {
        switch ( $strategy ) {
            case 'js':
                $escaped = htmlspecialchars($value, ENT_QUOTES, $charset);
                $escaped = json_encode($escaped);
                break;
            case 'url':
                $escaped = urlencode($value);
                break;
            case 'html':
            default:
                $escaped = htmlspecialchars($value, ENT_QUOTES, $charset);
        }

        unset($value, $strategy, $charset);
        return $escaped;
    }

}

# End of file