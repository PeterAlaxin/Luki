<?php
/**
 * Slice template filter adapter
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

class Slice
{

    public function Get($value, $start = 0, $length = 1)
    {
        switch (gettype($value)) {
            case 'string':
                $encoding = mb_detect_encoding($value);
                $slice = mb_substr($value, $start, $length, $encoding);
                break;
            case 'array':
                $slice = array_slice($value, $start, $length);
                break;
            default:
                $slice = $value;
        }

        return $slice;
    }
}
