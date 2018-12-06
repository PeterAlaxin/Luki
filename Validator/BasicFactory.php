<?php
/**
 * Validator factory
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

use Luki\Validator\BasicInterface;

abstract class BasicFactory implements BasicInterface
{
    public $isValid;
    private $error     = '';
    private $validator = '';
    private $message   = '';

    const ALPHA = 'a-zA-ZáäčďéěëíľňôóöŕřšťúůüýžÁÄČĎÉĚËÍĽŇÓÖÔŘŔŠŤÚŮÜÝŽ\ ';
    const NUM   = '0-9';
    const CHARS = '\r\n\+\-\*\\\.\,\:\;\%\(\)\/\?\!\&\=\_\@\#\$\^\{\}\"\'\|\`\<\>\~';

    public function __construct($options = array())
    {
        foreach ($options as $key => $value) {
            $this->$key = $value;
        }
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function isValid($value)
    {
        $this->isValid = false;

        if (1 === preg_match($this->validator, $value)) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%value%/', $value);
        }

        return $this->isValid;
    }

    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function fillMessage($from, $to)
    {
        $this->error = preg_replace($from, $to, $this->message);

        return $this;
    }

    public function setValidator($validator)
    {
        $this->validator = $validator;

        return $this;
    }

    public function getValidator()
    {
        return $this->validator;
    }

    public function setError($error = null)
    {
        if (empty($error)) {
            $error = $this->message;
        }

        $this->error = $error;

        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setNoError()
    {
        $this->error   = '';
        $this->isValid = true;

        return $this;
    }

    public function getValueLength($value)
    {
        if (is_string($value)) {
            $length = strlen($value);
        } elseif (is_array($value)) {
            $length = count($value);
        } else {
            $length = null;
        }

        return $length;
    }
}