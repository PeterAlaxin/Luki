<?php
/**
 * URL template filter adapter
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Template
 * @filesource
 */

namespace Luki\Template\Filters;

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

        return $url;
    }
}
