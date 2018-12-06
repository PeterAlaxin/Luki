<?php
/**
 * NotBlank validator
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

class NotBlank extends BasicFactory
{

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setMessage('The value "%value%" is blank!');
    }

    public function isValid($value)
    {
        $this->isValid = false;

        if (!empty($value)) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%value%/', $value);
        }

        return $this->isValid;
    }
}