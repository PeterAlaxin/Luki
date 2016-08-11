<?php
/**
 * Positive number validator
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

class NumberPositive extends BasicFactory
{

    public function __construct($options = array())
    {
        parent::__construct($options);

        $validator = '/^[0-9]*[.]?[0-9]*$/';
        $this->setValidator($validator);
        $this->setMessage('The value "%value%" is not valid positive number!');
    }
}
