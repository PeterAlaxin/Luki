<?php
/**
 * Length validator
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

class Length extends BasicFactory
{
    public $length = null;

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setMessage('The length is not equal "%length%"!');
    }

    public function isValid($value)
    {
        $this->isValid = false;
        $length        = $this->getValueLength($value);

        if ($length == $this->length) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%length%/', $this->length);
        }

        return $this->isValid;
    }

    public function setLength($lenght)
    {
        $this->length = (int) $lenght;
        return $this;
    }

    public function getLength()
    {
        return $this->length;
    }
}