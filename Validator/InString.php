<?php
/**
 * InString validator
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

class InString extends BasicFactory
{

    public $string = '';

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setMessage('The value "%value%" is not in the test string!');
    }

    public function isValid($value)
    {
        $this->isValid = false;

        if (1 == preg_match('/' . (string) $value . '/i', $this->string)) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%value%/', $value);
        }

        return $this->isValid;
    }

    public function setString($string)
    {
        $this->string = (string) $string;
    }

    public function getString()
    {
        return $this->string;
    }
}
