<?php
/**
 * Trim template filter adapter
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

class Trim
{

    public function Get($value, $charList = '')
    {
        $trimmed = trim($value, ' \t\n\r\0\x0B' . $charList);

        return $trimmed;
    }
}
