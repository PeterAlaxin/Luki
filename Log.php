<?php

/**
 * Log class
 *
 * Luki framework
 * Date 16.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki;

use Luki\Date;
use Luki\Log\Format\basicInterface as FormatInterface;
use Luki\Log\Writer\basicInterface as WriterInterface;

/**
 * Log class
 *
 * @package Luki
 */
class Log
{

    const EMERGENCY = 0;
    const ALERT = 1;
    const CRITICAL = 2;
    const ERROR = 3;
    const WARNING = 4;
    const NOTICE = 5;
    const INFO = 6;
    const DEBUG = 7;

    private $_priority = array(
      'emergency',
      'alert',
      'critical',
      'error',
      'warning',
      'notice',
      'info',
      'debug' );
    private $_format = NULL;
    private $_writer = NULL;
    private $_validators = array();
    private $_timestampFormat = 'c';

    public function __construct(FormatInterface $formatInterface, WriterInterface $writerInterface)
    {
        $this->_format = $formatInterface;
        $this->_writer = $writerInterface;

        unset($formatInterface, $writerInterface);
    }

    public static function findFormat($adapter)
    {
        $adapter = __NAMESPACE__ . '\Log\\Format\\' . $adapter;

        return $adapter;
    }

    public static function findWriter($adapter)
    {
        $adapter = __NAMESPACE__ . '\Log\\Writer\\' . $adapter;

        return $adapter;
    }

    public function addValidator($key, $validator)
    {
        $this->_validators[] = array(
          'key' => $key,
          'validator' => $validator );

        unset($key, $validator);
        return $this;
    }

    public function setFormat(FormatInterface $formatInterface)
    {
        $this->_format = $formatInterface;

        unset($formatInterface);
        return $this;
    }

    public function setWriter(WriterInterface $writerInterface)
    {
        $this->_writer = $writerInterface;

        unset($writerInterface);
        return $this;
    }

    public function setTimestampFormat($format)
    {
        $this->_timestampFormat = $format;

        unset($format);
        return $this;
    }

    public function Log($message, $priority)
    {
        $this->_Log($message, $priority);

        unset($message, $priority);
        return $this;
    }

    public function Emergency($message)
    {
        $this->_Log($message, self::EMERGENCY);

        unset($message);
        return $this;
    }

    public function Alert($message)
    {
        $this->_Log($message, self::ALERT);

        unset($message);
        return $this;
    }

    public function Critical($message)
    {
        $this->_Log($message, self::CRITICAL);

        unset($message);
        return $this;
    }

    public function Error($message)
    {
        $this->_Log($message, self::ERROR);

        unset($message);
        return $this;
    }

    public function Warning($message)
    {
        $this->_Log($message, self::WARNING);

        unset($message);
        return $this;
    }

    public function Notice($message)
    {
        $this->_Log($message, self::NOTICE);

        unset($message);
        return $this;
    }

    public function Info($message)
    {
        $this->_Log($message, self::INFO);

        unset($message);
        return $this;
    }

    public function Debug($message)
    {
        $this->_Log($message, self::DEBUG);

        unset($message);
        return $this;
    }

    private function _Log($message, $priority)
    {
        $now = date('Y-m-d H:i:s');
        $timestampFormat = Date::DateTimeToFormat($now, $this->_timestampFormat);
        $isValid = TRUE;

        $parameters = array(
          'timestamp' => $timestampFormat,
          'message' => $message,
          'priority' => $this->_priority[$priority],
          'priorityValue' => $priority
        );

        foreach ( $this->_validators as $validatorName ) {
            $value = $parameters[$validatorName['key']];
            $validator = $validatorName['validator'];
            $isValid = $validator->isValid($value);

            if ( !$isValid ) {
                break;
            }
        }

        if ( $isValid ) {
            $text = $this->_format->Transform($parameters);
            $this->_writer->Write($text);
        }

        unset($message, $priority, $now, $parameters, $message, $timestampFormat, $isValid, $value, $validator);
    }

}

# End of file