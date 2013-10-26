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
    
    const FILE_NOT_WRITABLE = 'File "%s" is not writable!';
    
    const CONFIGURATION_NOT_SAVED = 'File "%s" not saved!';

    /**
     * File name
     * @var string
     */
	public $File = '';
    
    /**
     * Configuration
     * @var array
     */
	public $Configuration = array();

	/**
	 * Constructor
	 * @param type $File
	 */
	public function __construct($File, $allowCreate = FALSE)
	{
        try {
            if(!is_file($File)) {
                $this->createConfigFile($File, $allowCreate);
            }

            if(!is_readable($File)) {
                throw new \Exception(sprintf(self::FILE_NOT_READABLE, $File));
            }

            $this->File = $File;
        }
        catch (\Exception $oException) {
            exit($oException->getMessage());
        }

		unset($File, $allowCreate);
	}

	/**
	 * Read configuration file
	 * @return array
	 */
	public function getConfiguration()
	{
		return $this->Configuration;
	}

	/**
	 * Read configuration file
	 * @return array
	 */
	public function getSections()
	{
        $Sections = array_keys($this->Configuration);
        
		return $Sections;
	}

	/**
	 * Save configuration to specific file
	 * @param array $aConfiguration Configuration
	 * @param string $sFileName File to store configuration
	 * @return boolean
	 */
	public function saveConfiguration()
	{
        if(!empty($this->File) and is_file($this->File) and !is_writable($this->File)) {
            throw new \Exception(sprintf(self::FILE_NOT_WRITABLE, $this->File));
        }        
	}

    /**
     * Get configuration file name
     * @return string
     */
	public function getFilename() {
		return $this->File;		
	}

    /**
     * Change actual configuration
     * @param array $Configuration
     * @return boolean
     */
	public function setConfiguration($Configuration)
	{
		$isSaved = FALSE;
        
		if(is_array($Configuration)) {
			$this->Configuration = $Configuration;
			$isSaved = TRUE;
		}

		unset($Configuration);
		return $isSaved;		
	}

    /**
     * Set file name
     * @param string $File
     * @return boolean
     */
	public function setFilename($File)
	{
		$isSaved = FALSE;
        
		if(!empty($File)) {
			$this->File = $File;
			$isSaved = TRUE;
		}

		unset($File);
		return $isSaved;		
	}
    
    public function saveToFile($Output)
    {
        $isSaved = FALSE;
        
        try {
            if(file_put_contents($this->File, $Output) === FALSE) {
                throw new \Exception(sprintf(self::CONFIGURATION_NOT_SAVED, $this->File));
            }

            $isSaved = TRUE;
        }
        catch (\Exception $oException) {
            exit($oException->getMessage());
        }
        
        unset($Output);
        return $isSaved;
    }

    public function createConfigFile($File, $allowCreate)
    {
        if(!$allowCreate) {
            throw new \Exception(sprintf(self::FILE_NOT_EXISTS, $File));        
        }
        
        if($this->setFilename($File)) {
            $this->saveConfiguration();
        }
        
        unset($File, $allowCreate);
    }
}

# End of file