<?php

/**
 * URL template filter adapter
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
 * URL template filter
 * 
 * @package Luki
 */
class Url
{

    public function Get($value)
    {
        $parts = parse_url($value);

        $url = empty($parts['scheme']) ? 'http://' : $parts['scheme'] . '://';
        $url .= empty($parts['user']) ? '' : $parts['user'] . ':';
        $url .= empty($parts['pass']) ? '' : $parts['pass'] . '@';
        $url .= empty($parts['host']) ? '' : $parts['host'] . '/';
        $url .= $parts['path'];
        $url .= empty($parts['query']) ? '' : '?' . $parts['query'];
        $url .= empty($parts['fragment']) ? '' : '#' . $parts['fragment'];

        unset($value, $parts);
        return $url;
    }

}

# End of file