<?php
/**
 * IPv6 validator
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

class IPv6 extends BasicFactory
{

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setMessage('The value "%value%" is not valid IPv6 address!');
    }

    public function isValid($value)
    {
        $this->isValid = false;

        if (false !== filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%value%/', $value);
        }

        return $this->isValid;
    }
}