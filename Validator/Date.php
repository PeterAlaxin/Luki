<?php
/**
 * Date validator
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

class Date extends BasicFactory
{

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setMessage('The value "%value%" is not valid date!');
    }

    public function isValid($value)
    {
        $this->isValid = false;

        if (false !== date_create($value)) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%value%/', $value);
        }

        return $this->isValid;
    }
}