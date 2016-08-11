<?php
/**
 * Validator interface
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

interface BasicInterface
{

    public function __construct($options);

    public function isValid($value);

    public function setMessage($message);

    public function getMessage();

    public function setValidator($validator);

    public function getValidator();

    public function getError();

    public function setNoError();

    public function getValueLength($value);
}
