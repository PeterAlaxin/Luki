<?php
/**
 * LengthMin validator
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

class LengthMin extends BasicFactory
{
    public $min = 0;

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setMessage('The length is less then "%min%"!');
    }

    public function isValid($value)
    {
        $this->isValid = false;
        $length        = $this->getValueLength($value);

        if ($length >= $this->min) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%min%/', $this->min);
        }

        return $this->isValid;
    }

    public function setMin($min)
    {
        $this->min = (float) $min;
        return $this;
    }

    public function getMin()
    {
        return $this->min;
    }
}