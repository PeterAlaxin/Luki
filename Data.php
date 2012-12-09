<?php

/**
 * Data class
 *
 * Luki framework
 * Date 9.12.2012
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
 * Data class
 *
 * Data access
 *
 * @package Luki
 */
class Luki_Data {

	private $oDataAdapter = NULL;

	/**
	 * Data constructor
	 */
	public function __construct($aOptions)
	{
		$sAdapterClass = 'Luki_Data_' . $aOptions['adapter'] . 'Adapter';
		$this->oDataAdapter = new $sAdapterClass($aOptions);

		unset($aOptions);
	}

	public function Select()
	{
		$oSelect = $this->oDataAdapter->Select();

		return $oSelect;
	}

	public function Query($sSelect)
	{
		$oResult = $this->oDataAdapter->Query($sSelect);

		unset($sSelect);
		return $oResult;
	}

}

# End of file