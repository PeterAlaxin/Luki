<?php

/**
 * Time class
 *
 * Luki framework
 * Date 30.11.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki;

use Luki\Date;

/**
 * Time class
 *
 * Time manipulation
 *
 * @package Luki
 */
class Time {

	public static $sFormat = 'H:i:s';
    
	public static $sTimeValidator = '/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/';
    
	private static $aSections = array();

	/**
	 * Set output time format
	 * 
	 * @param type $sFormat
	 */
	public static function setFormat($sFormat = 'H:i:s')
	{
		$bReturn = FALSE;

		$oDate = date_create('now');
		if(FALSE !== $oDate->format($sFormat)) {
			self::$sFormat = $sFormat;
			$bReturn = TRUE;
		}

		unset($sFormat);
		return $bReturn;
	}

	/**
	 * Get current format
	 */
	public static function getFormat()
	{
		return self::$sFormat;
	}

	/**
	 * Reset output time format to default
	 * 
	 * @param type $sFormat
	 */
	public static function resetFormat()
	{
		self::$sFormat = 'H:i:s';
	}

	public static function explodeMicrotime()
	{
		list($usec, $sec) = explode(" ", microtime());
		$nReturn = ((float) $usec + (float) $sec);

		unset($usec, $sec);
		return $nReturn;
	}

	public static function DateTimeToFormat($dDateTime, $sFormat = 'r')
	{
		$dDate = Date::DateTimeToFormat($dDateTime, $sFormat);

		unset($dDateTime, $sFormat);
		return $dDate;
	}

	public static function DateTimeToMicrotime($dDateTime)
	{
		$sMicro = Date::DateTimeToMicrotime($dDateTime);

		unset($dDateTime);
		return $sMicro;
	}

    public static function convertUtcToTimezone($dDateTime)
    {
        $sTimeZone = date_default_timezone_get();
        
        $dateTimeZoneHere = new \DateTimeZone($sTimeZone);
        $dateTimeZoneUTC = new \DateTimeZone("UTC");
        
        $dateTimeUTC = new \DateTime($dDateTime, $dateTimeZoneUTC);
        
        $nOffset = $dateTimeZoneHere->getOffset($dateTimeUTC);         
        $oInterval = new \DateInterval('PT' . abs($nOffset) . 'S');
        
        if($nOffset < 0) {
            $oInterval->invert = 1;
        }
        
        $dateTimeUTC->add($oInterval);
        $dateTimeHere = $dateTimeUTC->format('Y-m-d H:i:s');
        
        unset($dDateTime, $sTimeZone, $dateTimeZoneHere, $dateTimeZoneUTC, $dateTimeUTC, $nOffset, $oInterval);
        return $dateTimeHere;
    }
    
	/**
	 * Start stopwatch
	 * 
	 * @return float 
	 */
	public static function stopwatchStart($sSection = 'default', $aMicrotime = NULL)
	{
		$nReturn = FALSE;
        
		if(!empty($sSection)) {
            if(empty($aMicrotime)) {
                $aMicrotime = self::explodeMicrotime();
            }
            
			self::$aSections[$sSection] = array(
				'start' => $aMicrotime,
				'stop' => 0,
				'result' => 0);
			$nReturn = self::$aSections[$sSection]['start'];
		}
		
		unset($sSection);
		return $nReturn;
	}

	/**
	 * Get time when stopwatch started
	 * 
	 * @return float
	 */
	public static function getStopwatchStart($sSection = 'default')
	{
		$nReturn = FALSE;
		
		if(!empty(self::$aSections[$sSection])) {
			$nReturn = self::$aSections[$sSection]['start'];
		}
		
		unset($sSection);
		return $nReturn;
	}

	/**
	 * Stop stopwatch
	 * 
	 * @return float
	 */
	public static function stopwatchStop($sSection = 'default')
	{
		$nReturn = FALSE;
		
		if(!empty(self::$aSections[$sSection])) {
			self::$aSections[$sSection]['stop'] = self::explodeMicrotime();
			self::$aSections[$sSection]['result'] = self::$aSections[$sSection]['stop'] - self::$aSections[$sSection]['start'];
			$nReturn = self::$aSections[$sSection]['stop'];
		}
		
		unset($sSection);
		return $nReturn;
	}

	/**
	 * Get time when stopwatch stoped
	 * 
	 * @return float
	 */
	public static function getStopwatchStop($sSection = 'default')
	{
		$nReturn = FALSE;
		
		if(!empty(self::$aSections[$sSection])) {
			$nReturn = self::$aSections[$sSection]['stop'];
		}
		
		unset($sSection);
		return $nReturn;
	}

	/**
	 * Return stopwatch time in seconds
	 * 
	 * @return float
	 */
	public static function getStopwatch($sSection = 'default')
	{
		$nReturn = FALSE;
		
		if(!empty(self::$aSections[$sSection])) {
			$nReturn = self::$aSections[$sSection]['result'];
		}
		
		unset($sSection);
		return $nReturn;
	}

}

# End of file