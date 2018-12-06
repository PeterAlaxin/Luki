<?php
/**
 * UrlExist validator
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

class UrlExist extends BasicFactory
{

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->setMessage('The URL \'%value%\' does not exists!');
    }

    public function isValid($value)
    {
        $this->isValid = false;

        $headers = @get_headers($value);
        if (strpos($headers[0], '200') !== false) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%value%/', $value);
        }

        return $this->isValid;
    }
}