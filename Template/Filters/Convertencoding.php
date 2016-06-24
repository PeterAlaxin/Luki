<?php

/**
 * Convert encoding template filter adapter
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
 * Convert encoding template filter
 * 
 * @package Luki
 */
class Convertencoding
{

    protected $_list = array();

    public function __construct()
    {
        $this->_list = mb_list_encodings();
    }

    public function Get($value, $from = 'UTF-8', $to = 'ISO-8859-1')
    {
        if ( in_array($from, $this->_list) and in_array($to, $this->_list) ) {
            $converted = mb_convert_encoding($value, $to, $from);
        } else {
            $converted = $value;
        }

        unset($value);
        return $converted;
    }

}

# End of file