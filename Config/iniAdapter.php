<?php
/**
 * Config ini adapter
 *
 * Luki framework
 * Date 19.9.2012
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
 * Config ini adapter
 * 
 * @package Luki
 */
class Luki_Config_iniAdapter implements Luki_Config_Interface {

	private $sFileName = '';
	
	public function __construct($sFileName='')
	{
		if(is_file($sFileName)) {
			$this->sFileName = $sFileName;
		}
		
		unset($sFileName);
	}
	
	public function getConfiguration()
	{
		if(!empty($this->sFileName)) {
			$aConfiguration =  parse_ini_file($this->sFileName, TRUE);			
		}
		
		return $aConfiguration;
	}
	
	public function saveConfiguration($aConfiguration, $sFileName='')
	{
		$bReturn = FALSE;
		
		if(is_array($aConfiguration)) {
			if(empty($sFileName)) {
				$sFileName = $this->sFileName;
			}

			$sOutput = '';
			foreach($aConfiguration as $sSection => $aSectionValues) {
				$sOutput .= '[' . $sSection . ']' . chr(10); 
				foreach($aSectionValues as $sKey => $sValue) {
					$sOutput .= $sKey . ' = "' . $sValue . '"' . chr(10);
				}
				$sOutput .= chr(10); 
			}
			
			if(file_put_contents($sFileName, $sOutput) !== FALSE) { 
				$bReturn = TRUE;
			}
		}
		
		return $bReturn;
	}
}

# End of file