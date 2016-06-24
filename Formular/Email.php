<?php

/**
 * Email input
 *
 * Luki framework
 * Date 30.5.2014
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
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
 * Email input
 * 
 * @package Luki
 */
class Email extends basicFactory
{

    public function __construct($name, $label, $placeholder = '')
    {
        parent::__construct($name, $label, $placeholder);

        $this->setType('email');

        unset($name, $label, $placeholder);
    }

}

# End of file