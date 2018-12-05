<?php
/**
 * Replace template filter adapter
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

class Replace
{

    public function Get($value, $parameters)
    {
        foreach ($parameters as $from => $to) {
            $value = str_replace($from, $to, $value);
        }

        return $value;
    }
}