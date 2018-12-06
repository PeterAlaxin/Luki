<?php
/**
 * Email validator
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

class Email extends BasicFactory
{

    public function __construct($options = array())
    {
        parent::__construct($options);

        $validator = '/^([a-z0-9\+\._\/&!][-a-z0-9\+\._\/&!]*)@(([a-z0-9][-a-z0-9]*\.)([-a-z0-9]+\.)*[a-z]{2,})$/i';
        $this->setValidator($validator);
        $this->setMessage('The value "%value%" is not valid e-mail address!');
    }
}