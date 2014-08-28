<?php

/**
 * Alpha validator
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
 * Alpha validator
 * 
 * @package Luki
 */
class Alpha extends basicFactory
{
    public function __construct($options = array())
    {
        parent::__construct($options);
        
        $validator = '/^[' . self::ALPHA . ']*$/';
        $this->setValidator($validator);
        $this->setMessage('The value "%value%" contains characters other than letters!');
        
        unset($options, $validator);
    }

}

# End of file