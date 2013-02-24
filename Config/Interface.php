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

/**
 * Config Adapter interface
 * 
 * @package Luki
 */
interface Luki_Config_Interface {

	public function __construct($sFileName);

	public function setFilename($sFileName);

	public function getFilename();

	public function setConfiguration($aConfiguration);

	public function getConfiguration();

	public function saveConfiguration();
}

# End of file