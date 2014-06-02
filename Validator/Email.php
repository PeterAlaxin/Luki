<?php

/**
 * Email validator
 *
 * Luki framework
 * Date 14.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Validator
 * @filesource
 */

namespace Luki\Validator;

use Luki\Validator\basicFactory;

/**
 * Email validator
 * 
 * @package Luki
 */
class Email extends basicFactory
{

    public function __construct($options)
    {
        parent::__construct($options);

        $validator = '/^([a-z0-9\+\._\/&!][-a-z0-9\+\._\/&!]*)@(([a-z0-9][-a-z0-9]*\.)([-a-z0-9]+\.)*[a-z]{2,})$/i';
        $this->setValidator($validator);
        $this->setMessage('The value "%value%" is not valid e-mail address!');

        unset($options, $validator);
    }

}

# End of file