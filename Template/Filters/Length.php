<?php
/**
 * Length template filter adapter
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

class Length
{

    public function Get($value)
    {
        switch (gettype($value)) {
            case 'string':
                $length = strlen($value);
                break;
            case 'array':
                $length = count($value);
                break;
            case 'object':
                $length = 0;
                if (is_a($value, 'Iterator')) {
                    $length = iterator_count($value);
                }
                break;
            default:
                $length = $value;
        }

        return $length;
    }
}
