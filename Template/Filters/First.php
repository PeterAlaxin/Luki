<?php
/**
 * First template filter adapter
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

class First
{

    public function Get($value)
    {
        switch (gettype($value)) {
            case 'string':
                $first = mb_substr($value, 0, 1);
                break;
            case 'array':
                $first = array_shift(array_slice($value, 0, 1));
                break;
            default:
                $first = $value;
        }

        return $first;
    }
}