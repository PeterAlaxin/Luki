<?php
/**
 * Bites template filter adapter
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

class Bites
{

    public function Get($value)
    {
        if ($value < 1024) {
            $bites = number_format($value, 0) . '&nbsp;B';
        } elseif ($value < 1048576) {
            $bites = number_format($value / 1024, 2, ',', '.') . '&nbsp;kB';
        } elseif ($value < 1073741824) {
            $bites = number_format($value / 1048576, 2, ',', '.') . '&nbsp;MB';
        } else {
            $bites = number_format($value / 1073741824, 2, ',', '.') . '&nbsp;GB';
        }

        return $bites;
    }
}
