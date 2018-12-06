<?php
/**
 * InArray validator
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Validator
 * @filesource
 */

namespace Luki\Validator;

use Luki\Validator\BasicFactory;

class InArray extends BasicFactory
{
    public $values = array();

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setMessage('The value "%value%" is not in the test array!');
    }

    public function isValid($value)
    {
        $this->isValid = false;

        if (in_array($value, $this->values)) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%value%/', $value);
        }

        return $this->isValid;
    }

    public function setValues($values)
    {
        $this->values = (array) $values;
        return $this;
    }

    public function getValues()
    {
        return $this->values;
    }
}