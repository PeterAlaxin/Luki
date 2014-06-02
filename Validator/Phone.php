<?php

/**
 * Phone validator
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
 * Phone validator
 * 
 * @package Luki
 */
class Phone extends basicFactory
{

    public function __construct($options)
    {
        parent::__construct($options);

        $validator = '/^[+]?[0-9\ \/]*$/';
        $this->setValidator($validator);
        $this->setMessage('The value "%value%" is not valid phone number!');

        unset($options, $validator);
    }

}

# End of file