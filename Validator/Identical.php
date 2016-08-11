<?php
/**
 * Identical validator
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

class Identical extends BasicFactory
{

    public $token = null;

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setMessage('The value "%value%" not identical as "%token%"!');
    }

    public function isValid($value)
    {
        $this->isValid = false;

        if ($value === $this->token) {
            $this->setNoError();
        } else {
            $from = array('/%value%/', '/%token%/');
            $to = array($value, $this->token);
            $this->fillMessage($from, $to);
        }

        return $this->isValid;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }
}
