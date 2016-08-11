<?php
/**
 * Sort template filter adapter
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

class Sort
{

    public function Get($value)
    {
        switch (gettype($value)) {
            case 'array':
                asort($value);
                $sort = $value;
                break;
            default:
                $sort = $value;
        }

        return $sort;
    }
}
