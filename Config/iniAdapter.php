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

	private $aConfiguration = array();
	
	public function __construct($sFileName='')
	{
		if(is_file($sFileName)) {
			$this->aConfiguration =  parse_ini_file($sFileName, TRUE);
		}
		
		unset($sFileName);
	}
	
	public function getConfiguration()
	{
		return $this->aConfiguration;
	}
}

# End of file