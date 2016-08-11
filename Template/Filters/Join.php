<?php
/**
 * Join template filter adapter
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

class Join
{

    public function Get($value, $separator = '')
    {
        if (is_array($value)) {
            $join = implode($separator, $value);
        } else {
            $join = $value;
        }

        return $join;
    }
}
