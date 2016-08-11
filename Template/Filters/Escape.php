<?php
/**
 * Escape template filter adapter
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

class Escape
{

    public function Get($value, $strategy = 'html', $charset = 'UTF-8')
    {
        switch ($strategy) {
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

        return $escaped;
    }
}
