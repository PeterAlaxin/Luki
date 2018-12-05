<?php
/**
 * PasswordStrength validator
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

use Luki\Security;
use Luki\Validator\BasicFactory;

class PasswordStrength extends BasicFactory
{
    public $min = 0;

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setMessage('The password is too weak! Actual strength %score% is less than %min%.');
    }

    public function isValid($value)
    {
        $this->isValid = false;

        $score = Security::passwordStrength($value);

        if ($score < $this->min) {
            $from = array('/%score%/', '/%min%/');
            $to   = array($score, $this->min);
            $this->fillMessage($from, $to);
        } else {
            $this->setNoError();
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