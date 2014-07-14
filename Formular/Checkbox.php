<?php

/**
 * Checkbox input
 *
 * Luki framework
 * Date 30.5.2014
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Formular
 * @filesource
 */

namespace Luki\Formular;

use Luki\Formular\basicFactory;

/**
 * Checkbox input
 * 
 * @package Luki
 */
class Checkbox extends basicFactory
{

    public function __construct($name, $label, $placeholder = '')
    {
        parent::__construct($name, $label, $placeholder);

        $this->setType('checkbox');

        unset($name, $label, $placeholder);
    }

    public function setValue($value)
    {
        $this->_value = $value;
        if ( !empty($value) ) {
            $this->setAttribute('checked', 'checked');
        }

        unset($value);
        return $this;
    }

}

# End of file