<?php

/**
 * File Log Writer
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

namespace Luki\Log\Writer;

use Luki\Log\Writer\basicInterface;

/**
 * File Log Writer
 * 
 * @package Luki
 */
class File implements basicInterface {

	private $sFile = NULL;
	
	public function __construct($sFile)
	{
		$this->sFile = $sFile;
	}
	
	public function Write($sText)
	{
		if(is_array($sText)) {
			$sText = json_encode($sText);
		}
		
		file_put_contents($this->sFile, $sText . PHP_EOL, FILE_APPEND);
			
		unset($sText);
	}
}

# End of file