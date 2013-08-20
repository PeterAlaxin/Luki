<?php

/**
 * URL class for SEO
 *
 * Luki framework
 * Date 7.12.2012
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

/**
 * URL class for SEO
 *
 * @package Luki
 */
class Url {

	/**
	 * Make link
	 *
	 * @param mixed $xLink String for conversion
	 * @return sting
	 * @uses Storage::Set() Save Dispatcher object to Storage
	 * @uses Language::getTranslation() Get translation to file
	 * @uses Language::removeDiacritic() Remove diacritic from string
	 * @uses Dispatcher::makeCoolURL() Make cool url for SEO
	 */
	public static function makeLink($xLink)
	{
		$sLink = '';

		if(is_string($xLink)) {
			$sLink = html_entity_decode($xLink, ENT_QUOTES, 'UTF-8');
			$sLink = preg_replace('/[^a-z0-9- ]/i', '-', $sLink);
			$sLink = preg_replace('/ /', '-', $sLink);
			$sLink = strtolower($sLink);
		}
		elseif(is_array($xLink)) {

			foreach ($xLink as $xLinkPart) {
				$sLink .= self::makeLink((string) $xLinkPart) . '/';
			}

			unset($xLinkPart);
		}
		else {
			$sLink = self::makeLink((string) $xLink);
		}

		unset($xLink);
		return $sLink;
	}

    /**
	 * Reload page
	 *
	 * @param string Link to reload
	 * @param integer Response number
	 */
	static public function Reload($sLink = '', $nResponse = 302)
	{
		if(!empty($sLink)) {
    		header('Location: ' . $sLink, TRUE, $nResponse);
        	exit;
        }
	}
}

# End of file