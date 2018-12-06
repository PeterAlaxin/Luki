<?php
/**
 * LengthMax validator
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

class LengthMax extends BasicFactory
{
    public $max = 0;

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setMessage('The length is greater then "%max%"!');
    }

    public function isValid($value)
    {
        $this->isValid = false;
        $nLength       = $this->getValueLength($value);

        if ($nLength <= $this->max) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%max%/', $this->max);
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