<?php

/**
 * Basic request adapter
 *
 * Luki framework
 * Date 11.8.2013
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

namespace Luki\Request;

/**
 * Basic request adapter
 * 
 * @package Luki
 */
abstract class basicAdapter {

    public $aData = array();
    
    public function saveInputs($aInputs)
    {
        if(!empty($aInputs) and is_array($aInputs)) {

            foreach ($aInputs as $sKey => $xValue) {

                # Reverse automatic addslashes
                if(get_magic_quotes_gpc()) {
                    $xValue = stripslashes($xValue);
                }

                # HTML Special chars
                $xValue = htmlspecialchars($xValue, ENT_QUOTES);

                # Save input
                $this->aData[$sKey] = $xValue;
            }
        }

        unset($aInputs, $sKey, $xValue);
    }

    public function all()
    {
        return $this->aData;
    }
    
    public function keys()
    {
        $aKeys = array_keys($this->aData);
        
        return $aKeys;
    }
    
    public function add(array $aValues = array()) 
    {
        $this->aData = array_replace($this->aData, $aValues);
        
        unset($aValues);
    }
    
    public function replace(array $aValues = array()) 
    {
        $this->aData = $aValues;
        
        unset($aValues);
    }
    
    public function get($sKey, $xDefault = NULL)
    {
        if($this->has($sKey)) {
            $xDefault = $this->aData[$sKey];
        }
        
        unset($sKey);
        return $xDefault;
    }
    
    public function set($sKey, $xValue)
    {
        $this->add($sKey, $xValue);
    }
    
    public function has($sKey)
    {
        $bReturn = array_key_exists($sKey, $this->aData);
        
        unset($sKey);
        return $bReturn;
    }
    
    public function remove($sKey)
    {
        if($this->has($sKey)) {
            unset($this->aData[$sKey]);
        }
        
        unset($sKey);
    }
    
    public function getAlpha($sKey, $xDefault = NULL)
    {
        $xDefault = preg_replace('/[^[:alpha:]]/', '', $this->get($sKey, $xDefault));
        
        unset($sKey);
        return $xDefault;
    }
    
    public function getAlnum($sKey, $xDefault = NULL)
    {
        $xDefault = preg_replace('/[^[:alnum:]]/', '', $this->get($sKey, $xDefault));
        
        unset($sKey);
        return $xDefault;
    }
    
    public function getDigits($sKey, $xDefault = NULL)
    {
        $xDefault = preg_replace('/[^[:digit:]]/', '', $this->get($sKey, $xDefault));
        
        unset($sKey);
        return $xDefault;
    }
    
    public function getInt($sKey, $xDefault = NULL)
    {
        $xDefault = (int) $this->get($sKey, $xDefault);
        
        unset($sKey);
        return $xDefault;
    }
    
    public function filter($sKey, $xDefault = NULL, $nFilter = FILTER_DEFAULT, $xOptions = array())
    {
        $xDefault = filter_var($this->get($sKey, $xDefault), $nFilter, $xOptions);
        
        unset($sKey, $nFilter, $xOptions);
        return $xDefault;
    }
}

# End of file