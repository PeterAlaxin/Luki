<?php
/**
 * GreaterThan validator
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

class GreaterThan extends BasicFactory
{

    public $min = 0;

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setMessage('The value "%value%" not greater then "%min%"!');
    }

    public function isValid($value)
    {
        $this->isValid = false;

        if ($value > $this->min) {
            $this->setNoError();
        } else {
            $from = array('/%value%/', '/%min%/');
            $to = array($value, $this->min);
            $this->fillMessage($from, $to);
        }

        return $this->isValid;
    }

    public function setMin($min)
    {
        $this->min = (float) $min;
    }

    public function getMin()
    {
        return $this->min;
    }
}
