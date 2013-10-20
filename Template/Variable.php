<?php

/**
 * Template Variable class
 *
 * Luki framework
 * Date 6.4.2013
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

namespace Luki\Template;

use Luki\Template;

/**
 * Template Variable class
 *
 * @package Luki
 */
class Variable {

    protected $sContent = '';
    protected $sVariable = '';
    protected $sFunctionName = '';
    protected $sFunction = '';
    protected $sTransformedVariable = '';
    protected $sCode = '';
    protected $aFilters = array();
    protected $sFinalVariable = '';

    public function __construct($sContent)
    {
        $this->sContent = $sContent;

        $this->_prepareFilters();
        $this->_transformVariable();
        $this->_prepareVariable();

        unset($sContent);
    }

    public function getCode()
    {
        return $this->sCode;
    }

    public function getContent()
    {
        return $this->sContent;
    }

    public function getFunctionName()
    {
        return $this->sFunctionName;
    }

    public function getFunction()
    {
        $this->_prepareFunction();

        return $this->sFunction;
    }

    public function getVariable()
    {
        return $this->sFinalVariable;
    }

    private function _prepareFilters()
    {
        if(strpos($this->sContent, '|')) {
            $this->aFilters = explode('|', $this->sContent);
            $this->sVariable = array_shift($this->aFilters);
            $this->sFunctionName = 'fnc_' . sha1($this->sContent);
        }
        else {
            $this->sVariable = $this->sContent;
        }
    }

    private function _transformVariable()
    {
        $aMatches = array();
        $bArrayKeys = FALSE;
        preg_match('/^[\[{](.*)[\]}]$/', $this->sVariable, $aMatches);

        if(count($aMatches) > 0) {
            $aNewItems = array();
            $aItems = explode(', ', $aMatches[1]);

            foreach ($aItems as $Item) {
                if(strpos($Item, ': ')) {
                    $bArrayKeys = TRUE;
                    $aSubItems = explode(': ', $Item);

                    $aNewItems[] = $this->_stringToVariable($aSubItems[0]) .
                        ' => ' .
                        $this->_stringToVariable($aSubItems[1]);
                }
                else {
                    $aNewItems[] = $this->_stringToVariable($Item);
                }
            }

            $sVariable = preg_replace('/[\[{]/', 'array(', $this->sVariable);
            $sVariableArray = preg_replace('/' . $aMatches[1] . '/', implode(', ', $aNewItems), $sVariable);
            if($bArrayKeys) {
                $this->sTransformedVariable = preg_replace('/[}]/', ')', $sVariableArray);
            }
            else {
                $this->sTransformedVariable = preg_replace('/[\]}]/', ')', $sVariableArray);
            }
        }
        else {
            $this->sTransformedVariable = $this->_stringToVariable($this->sVariable);
        }

        unset($aMatches, $aNewItems, $aItems, $Item, $aSubItems, $sVariable, $sVariableArray, $bArrayKeys);
    }

    private function _stringToVariable($sString)
    {
        $aTypes = array('RangeOperator' => '/^(.*)\.\.(.*)$/',
          'SubArray' => '/^(.*)\.(.*)$/',
          'Range' => '/^range\((.*)\)$/',
          'Random' => '/^random\((.*)\)$/'
          );
        $sReturnString = '';
        
        if(!preg_match('/^[\'"]/', $sString) and !is_numeric($sString)) {
            
            foreach($aTypes as $sType => $sRegexp) {
                $aMatches = array();
                preg_match($sRegexp, $this->sVariable, $aMatches);
                
                if(!empty($aMatches)) {
                    switch($sType) {
                        case 'Random':
                            $sReturnString = '$this->aFunctions["random"]->Get(' . $aMatches[1] . ')';
                            break;
                        case 'Range':
                            $sReturnString = '$this->aFunctions["range"]->Get(' . $aMatches[1] . ')';
                            break;
                        case 'SubArray':
                            $aItem = explode('.', $aMatches[0]);
                            $sReturnString = '$this->aData["' . implode('"]["', $aItem) . '"]';
                            break;
                        case 'RangeOperator':
                            $aRange = explode('..', $sString);
                            $aNewArray = array();
                            if(is_numeric($aRange[0])) {
                                $nMin = min($aRange[0], $aRange[1]);
                                $nMax = max($aRange[0], $aRange[1]);
                                for($i = $nMin; $i <= $nMax; $i++) {
                                    $aNewArray[] = $i;
                                }
                            }
                            else {
                                $nMin = min(ord($aRange[0]), ord($aRange[1]));
                                $nMax = max(ord($aRange[0]), ord($aRange[1]));
                                for($i = $nMin; $i <= $nMax; $i++) {
                                    $aNewArray[] = chr($i);
                                }
                            }
                            $sReturnString = 'array("' . implode('","', $aNewArray) . '")';
                            break;
                    }
                    
                    break;
                }
            }
            
            if(empty($sReturnString)) {
                $sReturnString = '$this->aData["' . $sString . '"]';
            }    
        }
        else {
            $sReturnString = $sString;
        }
        
        unset($aMatches, $aRange, $sString, $sType, $sRegexp, $aItem);
        return $sReturnString;
    }

    private function _prepareVariable()
    {
        if(!empty($this->aFilters)) {
             $this->sFinalVariable = '$this->_' . $this->sFunctionName . '(' . $this->sTransformedVariable . ')';
        }
        else {
            $this->sFinalVariable = $this->sTransformedVariable;
        }

        $this->sCode = '<?php echo ' . $this->sFinalVariable . '; ?>';
    }
    
    private function _prepareFunction()
    {
        $aMatches = array();

        $sFunction = Template::phpRow('public function _' . $this->sFunctionName . '($xValue)');
        $sFunction .= Template::phpRow('{');
        foreach ($this->aFilters as $sFilter) {

            preg_match_all('|(.*)\((.*)\)|U', $sFilter, $aMatches, PREG_SET_ORDER);

            if(empty($aMatches)) {
                $sFunction .= Template::phpRow('$xValue = $this->aFilters["' . $sFilter . '"]->Get($xValue);', 2);
            }
            else {
                if(empty($aMatches[0][2])) {
                    $sFunction .= Template::phpRow('$xValue = $this->aFilters["' . $aMatches[0][1] . '"]->Get($xValue);', 2);
                }
                else {
                    $sParam = $aMatches[0][2];
                    $aSubMatches = array();
                    preg_match('/^[\[{](.*)[\]}]$/', $sParam, $aSubMatches);

                    if(count($aSubMatches) > 0) {
                        $sParam = preg_replace('/[\[{]/', 'array(', $sParam);
                        $sParam = preg_replace('/: /', ' => ', $sParam);
                        $sParam = preg_replace('/[\]}]/', ')', $sParam);
                    }

                    $sFunction .= Template::phpRow('$xValue = $this->aFilters["' . $aMatches[0][1] . '"]->Get($xValue, ' . $sParam . ');', 2);
                }
            }
        }
        $sFunction .= Template::phpRow('return $xValue;', 2);
        $sFunction .= Template::phpRow('}', 1, 2);

        $this->sFunction = $sFunction;

        unset($sFunction, $sFilter, $aMatches, $aSubMatches, $sParam);
    }

}

# End of file