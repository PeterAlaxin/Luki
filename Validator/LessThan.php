<?php
/**
 * LessThan validator
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

class LessThan extends BasicFactory
{
    public $max = 0;

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setMessage('The value "%value%" not less then "%max%"!');
    }

    public function isValid($value)
    {
        $this->isValid = false;

        if ($value < $this->max) {
            $this->setNoError();
        } else {
            $from = array('/%value%/', '/%max%/');
            $to   = array($value, $this->max);
            $this->fillMessage($from, $to);
        }

        return $this->isValid;
    }

    public function setMax($max)
    {
        $this->max = (float) $max;
        return $this;
    }

    public function getMax()
    {
        return $this->max;
    }
}