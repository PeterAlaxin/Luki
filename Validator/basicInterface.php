<?php

/**
 * Validator interface
 *
 * Luki framework
 * Date 14.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Validator
 * @filesource
 */

namespace Luki\Validator;

/**
 * Validator interface
 * 
 * @package Luki
 */
interface basicInterface
{

    public function __construct($options);

    public function isValid($value);

    public function setMessage($message);
    
    public function getMessage();

    public function setValidator($validator);
    
    public function getValidator();

    public function getError();

    public function setNoError();
    
    public function getValueLength($value);

}

# End of file