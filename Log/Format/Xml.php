<?php

/**
 * Xml Log Format adapter
 *
 * Luki framework
 * Date 16.12.2012
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

namespace Luki\Log\Format;

use Luki\Log\Format\basicInterface;

/**
 * Xml Log Format
 * 
 * @package Luki
 */
class Xml implements basicInterface
{

    public function __construct($format = '')
    {
        unset($format);
    }

    public function Transform($parameters)
    {
        $content = array();

        foreach ( $parameters as $key => $value ) {
            $content[$key] = $value;
        }

        unset($parameters, $key, $value);
        return $content;
    }

}

# End of file