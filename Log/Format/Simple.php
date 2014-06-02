<?php

/**
 * Simple Log Format adapter
 *
 * Luki framework
 * Date 16.12.2012
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

namespace Luki\Log\Format;

use Luki\Log\Format\basicInterface;

/**
 * Simple Log Format
 * 
 * @package Luki
 */
class Simple implements basicInterface
{

    private $_format = '';

    public function __construct($format = '')
    {
        if ( empty($format) ) {
            $format = '%timestamp%: %priority% (%priorityValue%): %message%';
        }

        $this->_format = $format;

        unset($format);
    }

    public function Transform($parameters)
    {
        $content = $this->_format;

        foreach ( $parameters as $key => $value ) {
            $content = preg_replace('/%' . $key . '%/', $value, $content);
        }

        unset($parameters, $key, $value);
        return $content;
    }

}

# End of file