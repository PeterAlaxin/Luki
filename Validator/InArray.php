<?php

/**
 * InArray validator
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
 * @subpackage Validator
 * @filesource
 */

namespace Luki\Validator;

use Luki\Validator\basicFactory;

/**
 * InArray validator
 * 
 * @package Luki
 */
class InArray extends basicFactory
{

    public $values = array();

    public function __construct($options)
    {
        parent::__construct($options);
        
        $this->setMessage('The value "%value%" is not in the test array!');
        
        unset($options);
    }



    /**
     * Validation
     * 
     * @param mixed $value 
     * @return bool
     */
    public function isValid($value)
    {
        $this->isValid = FALSE;

        if ( in_array($value, $this->values) ) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%value%/', $value);
        }

        unset($value);
        return $this->isValid;
    }
    
    public function setValues($values)
    {
        $this->values = (array) $values;

        unset($values);
    }

    public function getValues()
    {
        return $this->values;
    }


}

# End of file