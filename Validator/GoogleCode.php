<?php
/**
 * Google analytics code validator
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

class GoogleCode extends BasicFactory
{

    public function __construct($options = array())
    {
        parent::__construct($options);

        $validator = '/^UA-([0-9]{7,9})-([0-9]{1,2})$/';
        $this->setValidator($validator);
        $this->setMessage('The value "%value%" is not valid Google analytics code!');
    }
}