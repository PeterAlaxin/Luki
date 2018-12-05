<?php
/**
 * Random template function
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

namespace Luki\Template\Functions;

class Random
{

    public function Get($value = null)
    {
        switch (gettype($value)) {
            case 'string':
                $random = substr($value, mt_rand(0, strlen($value) - 1), 1);
                break;
            case 'array':
                $random = $value[mt_rand(0, count($value) - 1)];
                break;
            case 'integer':
                $random = mt_rand(0, $value);
                break;
            case 'null':
            default :
                $random = mt_rand();
        }

        return $random;
    }
}