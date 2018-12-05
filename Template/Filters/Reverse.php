<?php
/**
 * Reverse template filter adapter
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

class Reverse
{

    public function Get($value)
    {
        switch (gettype($value)) {
            case 'string':
                $value   = preg_split("//u", $value, null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                $reverse = implode('', array_reverse($value));
                break;
            case 'array':
                $reverse = array_reverse($value);
                break;
            default:
                $reverse = $value;
        }

        return $reverse;
    }
}