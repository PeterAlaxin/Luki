<?php

/**
 * Split template filter adapter
 *
 * Luki framework
 * Date 22.3.2013
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
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

    public function Get($value, $separator = '', $limit = 0)
    {
        switch ( gettype($value) ) {
            case 'string':
                if ( empty($separator) ) {
                    $values = preg_split("//u", $value, NULL, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

                    if ( $limit > 0 ) {
                        $newValue = array();
                        $count = 0;
                        $item = '';

                        foreach ( $values as $value ) {
                            $item .= $value;
                            $count++;

                            if ( $count == $limit ) {
                                $count = 0;
                                $newValue[] = $item;
                                $item = '';
                            }
                        }

                        if ( !empty($item) ) {
                            $newValue[] = $item;
                        }

                        $values = $newValue;
                    }
                } else {
                    $values = preg_split("/" . $separator . "/u", $value, $limit, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                }

                $split = $values;
                break;
            default:
                $split = $value;
        }

        unset($value, $values);
        return $split;
    }

}

# End of file