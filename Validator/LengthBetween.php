<?php
/**
 * LengthBetween validator
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

class LengthBetween extends BasicFactory
{

    public $min = 0;
    public $max = 0;

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setMessage('The length is not between "%min%" and "%max%"!');
    }

    public function isValid($value)
    {
        $this->isValid = false;
        $lenght = $this->getValueLength($value);

        if ($lenght >= $this->min and $lenght <= $this->max) {
            $this->setNoError();
        } else {
            $from = array('/%min%/', '/%max%/');
            $to = array($this->min, $this->max);
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

    public function setMax($max)
    {
        $this->max = (float) $max;
    }

    public function getMax()
    {
        return $this->max;
    }
}
