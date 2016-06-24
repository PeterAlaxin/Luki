<?php

/**
 * Split template filter adapter
 *
 * Luki framework
 * Date 22.3.2013
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki\Template\Filters;

/**
 * Split template filter
 * 
 * @package Luki
 */
class Split
{

    public function Get($value, $separator = '', $limit = 1)
    {
        switch ( gettype($value) ) {
            case 'string':
                if ( empty($separator) ) {
                    $split = $this->_splitWithoutSeparator($value, $limit);
                } else {
                    $split = $this->_splitWithSeparator($value, $separator, $limit);
                }
                break;
            default:
                $split = $value;
        }

        unset($value, $separator, $limit);
        return $split;
    }

    private function _splitWithoutSeparator($value, $limit)
    {
        if ( $limit > 0 ) {
            $split = array();
            $len = mb_strlen($value, "UTF-8");
            for ( $i = 0; $i < $len; $i += $limit ) {
                $split[] = mb_substr($value, $i, $limit, "UTF-8");
            }
        } else {
            $split = preg_split("//u", $value, -1, PREG_SPLIT_NO_EMPTY);
        }

        unset($value, $limit, $len, $i);
        return $split;
    }

    private function _splitWithSeparator($value, $separator, $limit)
    {
        $values = explode($separator, $value);

        if ( $limit > 1 ) {
            $split = array();
            $last = array();
            foreach ( $values as $key => $value ) {
                if ( $key + 1 < $limit ) {
                    $split[] = $value;
                } else {
                    $last[] = $value;
                }
            }
            $split[] = implode($separator, $last);
        } else {
            $split = $values;
        }

        unset($value, $separator, $limit, $values, $key, $last);
        return $split;
    }

}

# End of file