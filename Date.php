<?php
/**
 * Date class
 *
 * Luki framework
 * Date 30.11.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 ** @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

/**
 * Date class
 *
 * Date manipulation
 *
 * @package Luki
 */
class Luki_Date
{
	public static $sFormat = 'Y-m-d';
	
	public static $sDateValidator = '/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/';
		
	/**
	 * Set output date format
	 * 
	 * @param type $sFormat
	 */
	public static function setFormat($sFormat='Y-m-d')
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
	 * Reset output date format to default
	 * 
	 * @param type $sFormat
	 */
	public static function resetFormat()
	{
		self::$sFormat = 'Y-m-d';
	}
		
	/**
	 *  Validate date string
	 * 
	 * @param string $dDate
	 * @return boolean
	 */
	public static function validDate($dDate='now')
	{
		$bReturn = FALSE;
		
		if(FALSE !== date_create($dDate)) {
			$bReturn = TRUE;
		}
		
		unset($dDate);
		return $bReturn;
	}
	
	/**
	 * Add number of days to date
	 * 
	 * @param int $nDay
	 * @param date $dDate
	 * @return date
	 */
	public static function addDay($dDate=NULL, $nDay=NULL)
	{
		# Only first parameter
		if(is_null($nDay)) {
			if(is_null($dDate)) {
				$dDate = 'now';
				$nDay = 1;						
			}
			elseif(is_int($dDate)) {
				$nDay = $dDate;		
				$dDate = 'now';
			}
			elseif(is_string($dDate)) {
				$nDay = 1;		
			}
		}
		
		$xReturn = FALSE;
		$oInterval = new DateInterval('P0D');
		$oInterval->d = $oInterval->d + (int)$nDay;
		
		$oDate = date_create($dDate);
		if(FALSE !== $oDate) {
			$oDate->add($oInterval);
			$xReturn = $oDate->format(self::$sFormat);
		}
		
		unset($dDate, $nDay, $oInterval, $oDate);
		return $xReturn;
	}
	
	/**
	 * Add number of months top date
	 * 
	 * @param int $nMonth
	 * @param date $dDate
	 * @return date
	 */
	public static function addMonth($dDate=NULL, $nMonth=NULL)
	{
		# Only first parameter
		if(is_null($nMonth)) {
			if(is_null($dDate)) {
				$dDate = 'now';
				$nMonth = 1;						
			}
			elseif(is_int($dDate)) {
				$nMonth = $dDate;		
				$dDate = 'now';
			}
			elseif(is_string($dDate)) {
				$nMonth = 1;		
			}
		}
				
		$xReturn = FALSE;
		$oInterval = new DateInterval('P0M');
		$oInterval->m = $oInterval->m + (int)$nMonth;
		
		$oDate = date_create($dDate);
		if(FALSE !== $oDate) {
			$oDate->add($oInterval);
			$xReturn = $oDate->format(self::$sFormat);
		}
		
		unset($dDate, $nMonth, $oInterval, $oDate);
		return $xReturn;
	}

	/**
	 * Add number of years top date
	 * 
	 * @param int $nYear
	 * @param date $dDate
	 * @return date
	 */
	public static function addYear($dDate=NULL, $nYear=NULL)
	{
		# Only first parameter
		if(is_null($nYear)) {
			if(is_null($dDate)) {
				$dDate = 'now';
				$nYear = 1;						
			}
			elseif(is_int($dDate)) {
				$nYear = $dDate;		
				$dDate = 'now';
			}
			elseif(is_string($dDate)) {
				$nYear = 1;		
			}
		}
		
		$xReturn = FALSE;
		$oInterval = new DateInterval('P0Y');
		$oInterval->y = $oInterval->y + $nYear;
		
		$oDate = date_create($dDate);
		if(FALSE !== $oDate) {
			$oDate->add($oInterval);
			$xReturn = $oDate->format(self::$sFormat);
		}
		
		unset($dDate, $nYear, $oInterval, $oDate);
		return $xReturn;
	}
	
	/** 
	 * Create date
	 * 
	 * @param int $nYear
	 * @param int $nMonth
	 * @param int $nDay
	 * @return string
	 */
	public static function createDate($nYear=NULL, $nMonth=NULL, $nDay=NULL)
	{
		if(is_null($nYear)) {
			$nYear = date('Y');
		}
		if(is_null($nMonth)) {
			$nMonth = date('m');
		}
		if(is_null($nDay)) {
			$nDay = date('d');
		}
		
		$dDate = date(self::$sFormat, mktime(0, 0, 0, $nMonth, $nDay, $nYear));
		
		unset($nYear, $nMonth, $nDay);
		return $dDate;
	}
	
	/**
	 * Revert Date
	 * 
	 * @param string $dDate
	 * @param string $sOldDelimiter
	 * @param string $sNewDelimiter
	 * @return string
	 */
	public static function revertDate($dDate)
	{
		$xReturn = FALSE;
		$sOldDelimiter = NULL;				
		
		if(self::validDate($dDate)) {
			if(strpos($dDate, '.') !== FALSE) {
				$sOldDelimiter = '.';
				$sNewDelimiter = '-';
			}
			elseif(strpos($dDate, '-') !== FALSE) {
				$sOldDelimiter = '-';
				$sNewDelimiter = '.';
			}
			
			if(!is_null($sOldDelimiter)) {
				$aDate = array_reverse(explode($sOldDelimiter, $dDate));
				$xReturn = implode($sNewDelimiter, $aDate);
			}
			
		}
				
		unset($dDate, $sOldDelimiter, $sNewDelimiter, $aDate);
		return $xReturn;
	}
	
	/**
	 * Compute difference between dates
	 * 
	 * Thank`s Dave (http://www.addedbytes.com/blog/code/php-datediff-function/)
	 * 
	 * $interval can be: 
	 * yyyy - Number of full years 
	 * q - Number of full quarters 
	 * m - Number of full months 
	 * y - Difference between day numbers (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".) 
	 * d - Number of full days 
	 * w - Number of full weekdays 
	 * ww - Number of full weeks 
	 * h - Number of full hours 
	 * n - Number of full minutes 
	 * s - Number of full seconds (default) 
	 * 
	 * @param string $interval
	 * @param string $datefrom
	 * @param string $dateto
	 * @param bool $using_timestamps
	 * @return type
	 */
	public static function diffDate($interval, $datefrom, $dateto, $using_timestamps = FALSE) 
	{
		if (!$using_timestamps) { 
			$datefrom = strtotime($datefrom, 0); 
			$dateto = strtotime($dateto, 0); 
		} 
		
		$difference = $dateto - $datefrom; // Difference in seconds 
		switch($interval) { 
			case 'yyyy': // Number of full years 
				$years_difference = floor($difference / 31536000); 
				if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) { 
					$years_difference--; 
				} 
				if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) { 
					$years_difference++; 
				} 
				$datediff = $years_difference; 
				unset($years_difference);
				break; 
			case "q": // Number of full quarters 
				$quarters_difference = floor($difference / 8035200); 
				while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) { 
					$quarters_difference++; 
				} 
				$quarters_difference--; 
				$datediff = $quarters_difference; 
				unset($quarters_difference);
				break; 
			case "m": // Number of full months 
				$months_difference = floor($difference / 2678400); 
				while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) { 
					$months_difference++; 
				} 
				$months_difference--; 
				$datediff = $months_difference; 
				unset($months_difference);
				break; 
			case 'y': // Difference between day numbers 
				$datediff = date("z", $dateto) - date("z", $datefrom); 
				break; 
			case "d": // Number of full days 
				$datediff = floor($difference / 86400); 
				break; 
			case "w": // Number of full weekdays 
				$days_difference = floor($difference / 86400); 
				$weeks_difference = floor($days_difference / 7); // Complete weeks 
				$first_day = date("w", $datefrom); 
				$days_remainder = floor($days_difference % 7); 
				$odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder? 
				if ($odd_days > 7) { // Sunday 
					$days_remainder--; 
				} 
				if ($odd_days > 6) { // Saturday 
					$days_remainder--; 
				} 
				$datediff = ($weeks_difference * 5) + $days_remainder; 
				# Garbage
				unset($days_difference, $weeks_difference, $first_day, $days_remainder, $odd_days);
				break; 
			case "ww": // Number of full weeks 
				$datediff = floor($difference / 604800); 
				break; 
			case "h": // Number of full hours 
				$datediff = floor($difference / 3600); 
				break; 
			case "n": // Number of full minutes 
				$datediff = floor($difference / 60); 
				break; 
			default: // Number of full seconds (default) 
				$datediff = $difference; 
				break; 
			} 
			
		unset($interval, $datefrom, $dateto, $using_timestamps, $difference);
		return $datediff; 
	}
	
	/**
	 * Find nex working day
	 * 
	 * @param type $dDate
	 * @return type
	 */
	public static function nextWorkingDay($dDate=NULL) 
	{
		$xReturn = FALSE;
		if(is_null($dDate)) {
			$dDate = date('Y-m-d');
		}

		if(self::validDate($dDate)) {
			$nDay = strftime('%u', strtotime($dDate));
			$xReturn = $dDate;

			if($nDay > 5) {
				$dDate = self::addDay($dDate, 8-$nDay);
				$xReturn = self::nextWorkingDay($dDate);
			}
		}
		
		unset($nDay, $dDate);
		return $xReturn;
	}

	/**
	 * Convert DateTime to specific format
	 * 
	 * @param type $dDateTime
	 * @param type $sFormat
	 * @return type
	 */
	public static function DateTimeToFormat($dDateTime, $sFormat=NULL)
	{
		$sOldFormat = self::getFormat();
		$sMicro = self::DateTimeToMicrotime($dDateTime);
		$dDate = FALSE;
		
		if(is_null($sFormat)) {
			$sFormat = $sOldFormat;
		}

		if(FALSE !== $sMicro and self::setFormat($sFormat)) {
			$dDate = date(self::$sFormat, $sMicro);
		}
		
		self::setFormat($sOldFormat);
		
		unset($dDateTime, $sMicro, $sOldFormat, $sFormat);
		return $dDate;
	}

	/**
	 * Create microtime from date and time value
	 * Allowed format is Y-m-d H:i:s and Y-m-d
	 * 
	 * @uses Luki_Time::$sTimeValidator
	 * @param string $dDateTime
	 * @return int
	 */
	public static function DateTimeToMicrotime($dDateTime)
	{
		$sMicro = FALSE;
		$aDateTime = explode(' ', $dDateTime);

		if(1 === preg_match(self::$sDateValidator, $aDateTime[0])) {
			$aDate = explode('-', $aDateTime[0]);
		
			if(!isset($aDateTime[1])) {
				$aDateTime[1] = '00:00:00';
			}

			if(1 === preg_match(Luki_Time::$sTimeValidator, $aDateTime[1])) {
				$aTime = explode(':', $aDateTime[1]);
				$sMicro = mktime($aTime[0], $aTime['1'], $aTime[2], $aDate[1], $aDate[2], $aDate[0]);
			}
		}
		
		unset($dDateTime, $aDateTime, $aTime, $aDate);
		return $sMicro;
	}
}

# End of file