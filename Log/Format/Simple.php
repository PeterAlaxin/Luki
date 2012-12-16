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

/**
 * Simple Log Format
 * 
 * @package Luki
 */
class Luki_Log_Format_Simple implements Luki_Log_Format_Interface {

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