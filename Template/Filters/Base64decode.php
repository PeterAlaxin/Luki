<?php
/**
 * Base64decode template filter adapter
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

class Base64decode
{

    public function Get($value)
    {
        $string = base64_decode($value);

        return $string;
    }
}