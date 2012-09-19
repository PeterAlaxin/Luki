<?php
/**
 * Config xml adapter
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
 * Config xml adapter
 * 
 * @package Luki
 */
class Luki_Config_xmlAdapter implements Luki_Config_Interface {

	private $aConfiguration = array();
	
	private $sFile = '';
	
	private $sDefaultSection = '';
	
	public function __construct($sFileName='')
	{
	}
	
	public function getConfiguration()
	{	
		return $this->aConfiguration;
	}

	public function getConfigurationFile() {
		return $this->sFile;
	}
	
	public function setDefaultSection($sSection='')
	{
		
	}
	
	public function getSection($sSection='')
	{
		
	}
	
	public function getValue($sKey, $sSection)
	{
		
	}

}

# End of file