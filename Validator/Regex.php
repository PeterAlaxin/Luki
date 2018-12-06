<?php
/**
 * Regex validator
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

class Regex extends BasicFactory
{
    public $regex = null;

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setMessage('The value "%value%" does not match regular expression "%regex%"!');
    }

    public function isValid($value)
    {
        $this->isValid = false;

        if (1 === preg_match($this->regex, $value)) {
            $this->setNoError();
        } else {
            $from = array('/%value%/', '/%regex%/');
            $to   = array($value, $this->regex);
            $this->fillMessage($from, $to);
        }

        return $this->isValid;
    }

    public function setRegex($regex)
    {
        $this->regex = (float) $regex;
        return $this;
    }

    public function getRegex()
    {
        return $this->regex;
    }
}