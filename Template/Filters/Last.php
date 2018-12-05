<?php
/**
 * Last template filter adapter
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

class Last
{

    public function Get($value)
    {
        switch (gettype($value)) {
            case 'string':
                $last  = mb_substr($value, -1);
                break;
            case 'array':
                $value = array_reverse($value);
                $last  = array_slice($value, 0, 1);
                break;
            default:
                $last  = $value;
        }

        return $last;
    }
}