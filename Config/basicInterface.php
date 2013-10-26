<?php

/**
 * Config Adapter interface
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

namespace Luki\Config;

/**
 * Config Adapter interface
 * 
 * @package Luki
 */
interface basicInterface {

	public function __construct($File, $allowCreate);

	public function setFilename($File);

	public function getFilename();
    
	public function getSections();

	public function setConfiguration($aConfiguration);

	public function getConfiguration();

	public function saveConfiguration();
    
    public function createConfigFile($File, $allowCreate);
}

# End of file