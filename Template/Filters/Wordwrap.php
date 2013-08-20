<?php

/**
 * Wordwrap template filter adapter
 *
 * Luki framework
 * Date 22.3.2013
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

namespace Luki\Template\Filters;

/**
 * Wordwrap template filter
 * 
 * @package Luki
 */
class Wordwrap {

	public function Get($sValue, $nWidth = 75)
	{
		$sReturn = $this->_mb_wordwrap($sValue, $nWidth);
		
		unset($sValue, $nWidth);
		return $sReturn;
	}	

	private function _mb_wordwrap($str, $width = 75)
	{
		if(empty($str) or mb_strlen($str, 'UTF-8') <= $width) {
			return $str;
		}

		$break = chr(10);
		$br_width  = mb_strlen($break, 'UTF-8');
		$str_width = mb_strlen($str, 'UTF-8');
		$return = '';
		$last_space = false;

		for($i=0, $count=0; $i < $str_width; $i++, $count++)
		{
			// If we're at a break
			if (mb_substr($str, $i, $br_width, 'UTF-8') == $break)
			{
				$count = 0;
				$return .= mb_substr($str, $i, $br_width, 'UTF-8');
				$i += $br_width - 1;
				continue;
			}

			// Keep a track of the most recent possible break point
			if(mb_substr($str, $i, 1, 'UTF-8') == " ")
			{
				$last_space = $i;
			}

			// It's time to wrap
			if ($count >= $width)
			{
				// There are no spaces to break on!  Going to truncate :(
				if(!$last_space)
				{
					$return .= $break;
					$count = 0;
				}
				else
				{
					// Work out how far back the last space was
					$drop = $i - $last_space;

					// Cutting zero chars results in an empty string, so don't do that
					if($drop > 0)
					{
						$return = mb_substr($return, 0, -$drop);
					}

					// Add a break
					$return .= $break;

					// Update pointers
					$i = $last_space + ($br_width - 1);
					$last_space = false;
					$count = 0;
				}
			}

			// Add character from the input string to the output
			$return .= mb_substr($str, $i, 1, 'UTF-8');
		}
		
		return $return;
	}
}

# End of file