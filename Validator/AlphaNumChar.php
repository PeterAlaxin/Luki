<?php
/**
 * AlphaNumChar validator
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

class AlphaNumChar extends BasicFactory
{

    public function __construct($options = array())
    {
        parent::__construct($options);

        $validator = '/^[' . self::ALPHA . self::NUM . self::CHARS . ']*$/';
        $this->setValidator($validator);
        $this->setMessage('The value "%value%" contains characters other than letters or digits or allowed characters!');
    }
}
