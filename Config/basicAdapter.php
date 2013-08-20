<?php

/**
 * Basic config adapter
 *
 * Luki framework
 * Date 7.7.2013
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

use Luki\Config\basicInterface;

/**
 * Basic config adapter
 * 
 * @package Luki
 */
abstract class basicAdapter implements basicInterface {

    const FILE_NOT_EXISTS = 'File "%s" does not exists!';

    const FILE_NOT_READABLE = 'File "%s" is not readable!';
    
    const CONFIGURATION_NOT_SAVED = 'File "%s" not saved!';

    /**
     * File name
     * @var string
     */
	public $sFileName = '';
    
    /**
     * Configuration
     * @var array
     */
	public $aConfiguration = array();

	/**
	 * Constructor
	 * @param type $sFileName
	 */
	public function __construct($sFileName)
	{
        try {
            if(is_file($sFileName)) {
                if(is_readable($sFileName)) {
                    $this->sFileName = $sFileName;
                }
                else {
                    throw new \Exception(sprintf(self::FILE_NOT_READABLE, $sFileName));
                }
            }
            else {
                throw new \Exception(sprintf(self::FILE_NOT_EXISTS, $sFileName));
            }
        }
        catch (\Exception $oException) {
            exit($oException->getMessage());
        }

		unset($sFileName);
	}

	/**
	 * Read configuration file
	 * @return array
	 */
	public function getConfiguration()
	{
		return $this->aConfiguration;
	}

	/**
	 * Read configuration file
	 * @return array
	 */
	public function getSections()
	{
        $aSections = array_keys($this->aConfiguration);
        
		return $aSections;
	}

	/**
	 * Save configuration to specific file
	 * @param array $aConfiguration Configuration
	 * @param string $sFileName File to store configuration
	 * @return boolean
	 */
	public function saveConfiguration()
	{
	}

    /**
     * Get configuration file name
     * @return string
     */
	public function getFilename() {
		return $this->sFileName;		
	}

    /**
     * Change actual configuration
     * @param array $aConfiguration
     * @return boolean
     */
	public function setConfiguration($aConfiguration)
	{
		$bReturn = FALSE;
		if(is_array($aConfiguration)) {
			$this->aConfiguration = $aConfiguration;
			$bReturn = TRUE;
		}

		unset($aConfiguration);
		return $bReturn;		
	}

    /**
     * Set file name
     * @param string $sFileName
     * @return boolean
     */
	public function setFilename($sFileName)
	{
		$bReturn = FALSE;
		if(!empty($sFileName)) {
			$this->sFileName = $sFileName;
			$bReturn = TRUE;
		}

		unset($sFileName);
		return $bReturn;		
	}
    
    public function saveToFile($sOutput)
    {
        try {
            $bReturn = FALSE;
            if(file_put_contents($this->sFileName, $sOutput) !== FALSE) {
                $bReturn = TRUE;
            }
            else {
                throw new \Exception(sprintf(self::CONFIGURATION_NOT_SAVED, $this->sFileName));
            }
        }
        catch (\Exception $oException) {
            exit($oException->getMessage());
        }
        
        return $bReturn;
    }

}

# End of file