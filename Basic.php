<?php
/**
 * Basic class
 *
 * Luki framework
 * Date 22.09.2009
 *
 * @version 2.1.1
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 ** @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

/**
 * Abstract class
 *
 * Basic interface for other class
 *
 * @package Luki
 * @abstract Basic interface
 */
abstract class Basic
{
	/**
	 * Variables array
	 */
	private $aVariables = array();

	/**
	 * Destruct class and release data
	 *
	 * @uses Basic::$aVariables Variables array
	 */
    public function __destruct()
    {
    	unset($this->aVariables);
    }

    /**
     * Set value into object
     *
     * @param string $sName Variable name
     * @param mixed $xValue Variable value
	 * @uses Basic::$aVariables Variables array
     */
    public function __set($sName='', $xValue='')
    {
    	if(!empty($sName)) {
	    	$this->$sName = $xValue;
	   		$this->aVariables[] = $sName;
    	}
		
		# Garbage
		unset($sName, $xValue);
    }

	/**
	 * Get value from object
	 *
     * @param string $sName Variable name
	 * @return mixed Variable value
	 */
    public function __get($sName='')
    {
		# Define
		$xReturn = NULL;
		
    	# Is variable saved
    	if(!empty($sName) and isset($this->$sName)) {
			$xReturn = $this->$sName;
		}
		
		# Garbage
		unset($sName);
		
		# Return
		return $xReturn;
    }
    
    /**
	 * Format string
	 *
	 * @param Exception
	 * @return string Formated string
	 */
	public function __toString() {
		return serialize($this);
	}

	/**
	 * Show debug information about class
	 *
	 * @uses DEBUG Debuging flag
	 * @uses Basic::$aVariables Variables array
	 */
	public function Debug()
	{
		# Debuging enabled
		if(defined('DEBUG') and DEBUG) {
			# Begin
			echo '<pre>';
			echo '<b>Variables in class: ' . get_class($this) . '</b><br />';

			# Fill values
			foreach($this->aVariables as $sVariable) {
				if(isset($this->$sVariable)) {
					echo '<b>' . $sVariable . '</b><br />';
					echo var_dump($this->$sVariable);
				}
			}

			# Fill methods
			$aMethods = get_class_methods(get_class($this));
			echo '<b>Methods in class: ' . get_class($this) . '</b><br />';
			foreach($aMethods as $sMethod) {
				echo $sMethod . '<br />';
			}

			# End
			echo '</pre><hr />';
			
			# Garbage
			unset($sVariable, $aMethods, $sMethod);
		}
	}

}

# End of file