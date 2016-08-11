<?php
/**
 * Urlencode template filter adapter
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

class Urlencode
{

    public function Get($value)
    {
        $encoded = urlencode($value);

        return $encoded;
    }
}
