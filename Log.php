<?php
/**
 * Log class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Log
 * @filesource
 */

namespace Luki;

use Luki\Date;
use Luki\Log\Format\BasicInterface as Format;
use Luki\Log\Writer\BasicInterface as Writer;

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
        'debug');
    private $format = null;
    private $writer = null;
    private $validators = array();
    private $timestampFormat = 'c';

    public function __construct(Format $format, Writer $writer)
    {
        $this->format = $format;
        $this->writer = $writer;
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
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
        $this->validators[] = array('key' => $key, 'validator' => $validator);

        return $this;
    }

    public function setFormat(Format $format)
    {
        $this->format = $format;

        return $this;
    }

    public function setWriter(Writer $writer)
    {
        $this->writer = $writer;

        return $this;
    }

    public function setTimestampFormat($timestampFormat)
    {
        $this->timestampFormat = $timestampFormat;

        return $this;
    }

    public function Log($message, $priority)
    {
        $this->addToLog($message, $priority);

        return $this;
    }

    public function Emergency($message)
    {
        $this->addToLog($message, self::EMERGENCY);

        return $this;
    }

    public function Alert($message)
    {
        $this->addToLog($message, self::ALERT);

        return $this;
    }

    public function Critical($message)
    {
        $this->addToLog($message, self::CRITICAL);

        return $this;
    }

    public function Error($message)
    {
        $this->addToLog($message, self::ERROR);

        return $this;
    }

    public function Warning($message)
    {
        $this->addToLog($message, self::WARNING);

        return $this;
    }

    public function Notice($message)
    {
        $this->addToLog($message, self::NOTICE);

        return $this;
    }

    public function Info($message)
    {
        $this->addToLog($message, self::INFO);

        return $this;
    }

    public function Debug($message)
    {
        $this->addToLog($message, self::DEBUG);

        return $this;
    }

    private function addToLog($message, $priority)
    {
        $now = date('Y-m-d H:i:s');
        $timestampFormat = Date::DateTimeToFormat($now, $this->timestampFormat);
        $isValid = true;

        $parameters = array(
            'timestamp' => $timestampFormat,
            'message' => $message,
            'priority' => $this->_priority[$priority],
            'priorityValue' => $priority
        );

        foreach ($this->validators as $validatorName) {
            $value = $parameters[$validatorName['key']];
            $validator = $validatorName['validator'];
            $isValid = $validator->isValid($value);

            if (!$isValid) {
                break;
            }
        }

        if ($isValid) {
            $text = $this->format->Transform($parameters);
            $this->writer->Write($text);
        }
    }
}
