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

	public function __construct($sFileName='')
	{
		if(is_file($sFileName)) {
			libxml_use_internal_errors(TRUE);
			$oXML = simplexml_load_file($sFileName, 'SimpleXMLElement', LIBXML_NOERROR);
			var_dump($oXML);
			exit;
		}
	}
	
	public function getConfiguration()
	{	
		return $this->aConfiguration;
	}

}

# End of file