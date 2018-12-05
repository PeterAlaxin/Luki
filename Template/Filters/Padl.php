<?php
/**
 * Pad left template filter adapter
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

class Padl
{

    public function Get($value, $length = 1, $string = "-")
    {
        $pad = str_pad($value, $length, $string, STR_PAD_LEFT);

        return $pad;
    }
}