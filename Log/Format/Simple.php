<?php

/**
 * Simple Log Format adapter
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

namespace Luki\Log\Format;

use Luki\Log\Format\basicInterface;

/**
 * Simple Log Format
 * 
 * @package Luki
 */
class Simple implements basicInterface {

	private $sFormat = '';
	
	public function __construct($sFormat='')
	{
		if(empty($sFormat)) {
			$sFormat = '%timestamp%: %priority% (%priorityValue%): %message%';
		}
		
		$this->sFormat = $sFormat;
		
		unset($sFormat);
	}
	
	public function Transform($aParameters)
	{
		$sText = $this->sFormat;
		
		foreach($aParameters as $sKey => $sValue) {
			$sText = preg_replace('/%' . $sKey . '%/', $sValue, $sText);			
		}
		
		unset($aParameters, $sKey, $sValue);
		return $sText;
	}
}

# End of file