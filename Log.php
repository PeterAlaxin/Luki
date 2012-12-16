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

/**
 * Log class
 *
 * @package Luki
 */
class Luki_Log {

	const EMERGENCY = 0;
	const ALERT = 1;
	const CRITICAL = 2;
	const ERROR = 3;
	const WARNING = 4;
	const NOTICE = 5;
	const INFO = 6;
	const DEBUG = 7;

	private $aPriority = array(
		'emergency',
		'alert',
		'critical',
		'error',
		'warning',
		'notice',
		'info',
		'debug');
	private $oFormat = NULL;
	private $oWriter = NULL;
	private $aValidators = array();
	private $sTimestampFormat = 'c';

	public function __construct()
	{
		$this->oFormat = new Luki_Log_Format_Simple();
		$this->oWriter = new Luki_Log_Writer_Simple();
	}

	public function addValidator($sKey, $oValidator)
	{
		$this->aValidators[] = array(
			'key' => $sKey,
			'validator' => $oValidator);

		unset($sKey, $oValidator);
		return $this;
	}

	public function setFormat($oFormat)
	{
		$this->oFormat = $oFormat;

		unset($oFormat);
		return $this;
	}

	public function setWriter($oWriter)
	{
		$this->oWriter = $oWriter;

		unset($oWriter);
		return $this;
	}

	public function setTimestampFormat($sFormat)
	{
		$this->sTimestampFormat = $sFormat;

		unset($sFormat);
		return $this;
	}

	public function Log($sMessage, $nPriority)
	{
		$this->_Log($sMessage, $nPriority);

		unset($sMessage, $nPriority);
	}

	public function Emergency($sMessage)
	{
		$this->_Log($sMessage, self::EMERGENCY);

		unset($sMessage);
	}

	public function Alert($sMessage)
	{
		$this->_Log($sMessage, self::ALERT);

		unset($sMessage);
	}

	public function Critical($sMessage)
	{
		$this->_Log($sMessage, self::CRITICAL);

		unset($sMessage);
	}

	public function Error($sMessage)
	{
		$this->_Log($sMessage, self::ERROR);

		unset($sMessage);
	}

	public function Warning($sMessage)
	{
		$this->_Log($sMessage, self::WARNING);

		unset($sMessage);
	}

	public function Notice($sMessage)
	{
		$this->_Log($sMessage, self::NOTICE);

		unset($sMessage);
	}

	public function Info($sMessage)
	{
		$this->_Log($sMessage, self::INFO);

		unset($sMessage);
	}

	public function Debug($sMessage)
	{
		$this->_Log($sMessage, self::DEBUG);

		unset($sMessage);
	}

	private function _Log($sMessage, $nPriority)
	{
		$dNow = date('Y-m-d H:i:s');
		$sTimestampFormat = Luki_Date::DateTimeToFormat($dNow, $this->sTimestampFormat);
		$bValid = TRUE;

		$aParameters = array(
			'timestamp' => $sTimestampFormat,
			'message' => $sMessage,
			'priority' => $this->aPriority[$nPriority],
			'priorityValue' => $nPriority
		);

		foreach ($this->aValidators as $aValidator) {
			$sValue = $aParameters[$aValidator['key']];
			$oValidator = $aValidator['validator'];
			$bValid = $oValidator->isValid($sValue);
			
			if(!$bValid) {
				break;
			}
		}

		if($bValid) {
			$sText = $this->oFormat->Transform($aParameters);
			$this->oWriter->Write($sText);
		}

		unset($sMessage, $nPriority, $dNow, $aParameters, $sMessage, $sTimestampFormat, $bValid, $sValue, $oValidator);
	}

}

# End of file