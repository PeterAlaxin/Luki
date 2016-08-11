<?php
/**
 * Keys template filter adapter
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

class Keys
{

    public function Get($value)
    {
        if (is_array($value)) {
            $keys = array_keys($value);
        } else {
            $keys = $value;
        }

        return $keys;
    }
}
