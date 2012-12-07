<?php

/**
 * Session class
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

/**
 * Session class
 *
 * Session management
 *
 * @package Luki
 */
class Luki_Session {

	public static $aLimiters = array('public', 'private_no_expire', 'private', 'nocache');

	/**
	 * Start session
	 *
	 * @return string Actual Session ID
	 * @uses PROGRAM Program name for Session name
	 */
	static public function Start($sType = 'nocache')
	{
		if(!in_array($sType, self::$aLimiters)) {
			$sType = 'nocache';
		}

		session_cache_limiter($sType);
		session_set_cookie_params(0, "/", NULL, FALSE, TRUE);

		session_start();
		$sID = session_id();

		unset($sType);
		return $sID;
	}

	/**
	 * Restart session
	 *
	 * @return array Old and new Session ID
	 * @uses Session::Start() Define first session
	 */
	static public function Restart()
	{
		if(!isset($_SESSION)) {
			self::Start();
		}

		$aReturn = array('old' => session_id());

		session_regenerate_id(TRUE);
		$aReturn['new'] = session_id();

		return $aReturn;
	}

	/**
	 * Destroy session
	 *
	 * @uses Session::Restart() Destroy existing session and create new
	 */
	static public function Destroy()
	{
		$bReturn = FALSE;
		$sSessionName = session_name();
		$sSessionCookie = session_get_cookie_params();

		self::Restart();
		if(session_destroy()) {
			setcookie(
				$sSessionName, 
				false, 
				$sSessionCookie['lifetime'], 
				$sSessionCookie['path'], 
				$sSessionCookie['domain'], 
				$sSessionCookie['secure']
			);
			$bReturn = TRUE;
		}

		unset($sSessionCookie, $sSessionName);
		return $bReturn;
	}

}

# End of file