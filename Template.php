<?php

/**
 * Template class
 *
 * Luki framework
 * Date 6.1.2013
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

namespace Luki;

use Luki\File;
use Luki\Template\Block;
use Luki\Template\Filters\Capitalize;
use Luki\Time;

/**
 * Template class
 *
 * @package Luki
 */
class Template {

    protected static $sTwigPath = '';
    protected $sTemplate = '';
    protected $sClass = '';
    protected $sClassContent = '';
    protected $aData = array();
    protected $aVariables = array();
    protected $aBlocks = array();
    protected $sTwig = '';
    protected $sNewClass = '';
    protected $aFilters = array();
    protected $aFunctions = array();
    protected $sExtendedClass = '';

    /**
     * Constructor
     *
     * @param string $sFileName Template file with path
     * @uses Template::_loadConfiguration() Load configuration
     * @uses Template::_loadTemplate() Load template
     * @uses Template::_deleteMemo() Delete memos from template
     * @uses Template::_explodeTemplate() Explode template
     * @uses Template::_transformConstants() Transform constants
     */
    function __construct($sTemplate, $aData)
    {
        $this->sTemplate = $sTemplate;
        $this->aData = (array) $aData;

        $sTemplateWithoutTwig = preg_replace('/.twig/', '', $sTemplate);
        $sTemplateWithoutTemplate = preg_replace('/\/template/', '', $sTemplateWithoutTwig);
        $sClass = ucwords(preg_replace('/\//', ' ', $sTemplateWithoutTemplate));  
        
        $this->sClass = implode('', array_slice(explode(' ', $sClass), -2));
        
        if(Storage::isProfiler()) {
            Time::stopwatchStart('Luki_Template_' . $this->sClass);
        }

        $this->sNewClass = self::$sTwigPath . preg_replace('/_/', '/', $this->sClass) . '.php';
        if(!file($this->sNewClass) or filectime($this->sTemplate) != filectime($this->sNewClass)) {
            $this->_generateTemplate();
        }

        unset($sTemplate, $sTemplateWithoutTwig, $sTemplateWithoutTemplate, $aData, $sClass);
    }

    public static function setPath($sNewPath)
    {
        self::$sTwigPath = $sNewPath;
    }
    
    public function Render()
    {
        $oTemplateClass = new $this->sClass($this->aData);
        
        ob_start();
        $oTemplateClass->Render();
        $sOutput = ob_get_contents();
        ob_end_clean();
        
        if(Storage::isProfiler()) {
            Time::stopwatchStop('Luki_Template_' . $this->sClass);
            Storage::Profiler()->Add('Template', array('name' => $this->sClass, 'time' => Time::getStopwatch('Luki_Template_' . $this->sClass)));
        }

        return $sOutput;
    }

    private function _generateTemplate()
    {
        $this->sTwig = file_get_contents($this->sTemplate);

        $this->_clearComments();
        
        $this->_extendClass();
        $this->_begin();

        if(empty($this->sExtendedClass)) {
            $this->_defineFilters();
            $this->_defineFunctions();
        }
        
        $this->_defineBlocks();
        $this->_addFunctions();
        $this->_useBlocks();
        $this->_end();

        file_put_contents($this->sNewClass, $this->sClassContent);
    }

    private function _clearComments()
    {
        $aMatches = array();
        preg_match_all('|{# (.*) #}|U', $this->sTwig, $aMatches, PREG_SET_ORDER);

        foreach ($aMatches as $aMatch) {
            $this->sTwig = str_replace($aMatch[0], '', $this->sTwig);
        }

        unset($aMatches, $aMatch);
    }

    private function _extendClass()
    {
        $aMatches = array();
        preg_match_all('|{% extends "(.*)" %}|U', $this->sTwig, $aMatches, PREG_SET_ORDER);

        if(!empty($aMatches) and !empty($aMatches[0][1])) {
            
            $aExtended = explode('/', $aMatches[0][1]);
            $sExtendedClass = Loader::isFile($aExtended[0] . '/template/' . $aExtended[1] . '.twig');
            $oTemplate = new Template($sExtendedClass, array()); 
            
            foreach($aExtended as $nKey => $sExtended) {
                $aExtended[$nKey] = Capitalize::Get($sExtended);
            }
            
            $this->sExtendedClass = implode('', $aExtended);
        }

        foreach ($aMatches as $aMatch) {
            $this->sTwig = str_replace($aMatch[0], '', $this->sTwig);
        }

        unset($aMatches, $aMatch, $aExtended, $sExtendedClass, $oTemplate, $nKey, $sExtended);
    }
    
    private function _begin()
    {
        $this->sClassContent = self::phpRow('<?php', 0);
        
        if(!empty($this->sExtendedClass)) {
            $this->sClassContent .= self::phpRow('class ' . $this->sClass . ' extends ' . $this->sExtendedClass, 0);
            $this->sClassContent .= self::phpRow('{', 0, 2);
            $this->sClassContent .= self::phpRow('public function __construct($aData)');
            $this->sClassContent .= self::phpRow('{');
            $this->sClassContent .= self::phpRow('parent::__construct($aData);', 2);
            $this->sClassContent .= self::phpRow('}', 1, 2);
        }
        else {          
            $this->sClassContent .= self::phpRow('class ' . $this->sClass, 0);
            $this->sClassContent .= self::phpRow('{', 0, 2);
            $this->sClassContent .= self::phpRow('public $aFilters = array();', 1, 2);
            $this->sClassContent .= self::phpRow('public $aFunctions = array();', 1, 2);
            $this->sClassContent .= self::phpRow('public $aData = array();', 1, 2);
            $this->sClassContent .= self::phpRow('public $aLoop = array();', 1, 2);
            $this->sClassContent .= self::phpRow('public function __construct($aData)');
            $this->sClassContent .= self::phpRow('{');
            $this->sClassContent .= self::phpRow('$this->aData = $aData;', 2);
            $this->sClassContent .= self::phpRow('$this->aData["loop"] = array();', 2);
            $this->sClassContent .= self::phpRow('$this->_defineFilters();', 2);
            $this->sClassContent .= self::phpRow('$this->_defineFunctions();', 2);
            $this->sClassContent .= self::phpRow('}', 1, 2);
            $this->sClassContent .= self::phpRow('public function Render()');
            $this->sClassContent .= self::phpRow('{');
            $this->sClassContent .= self::phpRow('$this->_mainBlock();', 2);
            $this->sClassContent .= self::phpRow('}', 1, 2);
        }        
    }

    private function _end()
    {
        $this->sClassContent .= self::phpRow('}', 0, 0);
    }

    private function _defineFilters()
    {
        $this->sClassContent .= self::phpRow('public function _defineFilters()');
        $this->sClassContent .= self::phpRow('{');

        $aFiles = File::getFilesInDirectory(__DIR__ . '/Template/Filters');

        foreach ($aFiles as $sFile) {
            $sFile = preg_replace('/.php/', '', $sFile);
            $sFilter = strtolower($sFile);
            $this->sClassContent .= self::phpRow('$this->aFilters["' . $sFilter . '"] = new Luki\\Template\\Filters\\' . $sFile . ';', 2);
            $this->aFilters[] = $sFilter;
        }
        $this->sClassContent .= self::phpRow('}', 1, 2);

        unset($aFiles, $sFile, $sFilter);
    }

    private function _defineFunctions()
    {
        $this->sClassContent .= self::phpRow('public function _defineFunctions()');
        $this->sClassContent .= self::phpRow('{');

        $aFiles = File::getFilesInDirectory(__DIR__ . '/Template/Functions');

        foreach ($aFiles as $sFile) {
            $sFile = preg_replace('/.php/', '', $sFile);
            $sFunction = strtolower($sFile);
            $this->sClassContent .= self::phpRow('$this->aFunctions["' . $sFunction . '"] = new Luki\\Template\\Functions\\' . $sFile . ';', 2);
        }
        $this->sClassContent .= self::phpRow('}', 1, 2);

        unset($aFiles, $sFile, $sFunction);
    }

    private function _defineBlocks()
    {
        $sMainBlock = $this->_parseBlocks($this->sTwig);
        
        if(empty($this->sExtendedClass)) {
            $this->aBlocks['main'] = new Block($sMainBlock);
        }
        
        foreach ($this->aBlocks as $sName => $oBlock) {
            $sBlockContent = $this->_getContent($sName, $oBlock);
            $this->sClassContent .= self::phpRow('public function _' . $sName . 'Block()');
            $this->sClassContent .= self::phpRow('{');
            $this->sClassContent .= self::phpRow(' ?>' . $sBlockContent . '<?php ', 2);
            $this->sClassContent .= self::phpRow('}', 1, 2);

            $this->aVariables = array_merge($this->aVariables, $oBlock->getVariables());
        }

        unset($sName, $oBlock, $sMainBlock);
    }

    private function _getContent($sName, $oBlock)
    {
        if(empty($this->sExtendedClass)) {
            $sContent = preg_replace('/{% parent %}/', '', $oBlock->getContent());
        }
        else {
            $sContent = preg_replace('/{% parent %}/', self::phpRow('<?php parent::_' . $sName . 'Block(); ?>'), $oBlock->getContent());            
        }
        
        unset($sName, $oBlock);
        
        return $sContent;
    }
    
    private function _parseBlocks($sBlock)
    {
        $aStartMatches = array();
        $aEndMatches = array();
        $aBlockMatches = array();

        preg_match_all('|({% block (.*) %})|U', $sBlock, $aStartMatches, PREG_SET_ORDER);
        preg_match_all('|({% endblock(.*) %})|U', $sBlock, $aEndMatches, PREG_SET_ORDER);

        if(count($aStartMatches) != count($aEndMatches)) {
            echo 'Template error in block counts';
            exit;
        }

        while (count($aStartMatches) > 0) {
            foreach ($aStartMatches as $aBlock) {
                preg_match_all('|({% block ' . $aBlock[2] . ' %})([\s\S]*)({% endblock(.*) %})|U', $sBlock, $aBlockMatches, PREG_SET_ORDER);

                foreach ($aBlockMatches as $aSubBlock) {
                    if(0 === preg_match_all('/{% block (.*) %}/', $aSubBlock[2], $aSubBlockMatches)) {
                        $this->aBlocks[$aBlock[2]] = new Block($aSubBlock);
                        $sBlock = str_replace($aSubBlock[0], '<?php $this->_' . $aBlock[2] . 'Block(); ?>', $sBlock);
                    }
                }
            }

            preg_match_all('|({% block (.*) %})|U', $sBlock, $aStartMatches, PREG_SET_ORDER);
        }

        return $sBlock;
    }

    private function _addFunctions()
    {
        foreach ($this->aVariables as $oVariable) {

            $sFunctionName = $oVariable->getFunctionName();

            if(!empty($sFunctionName) and !in_array($sFunctionName, $this->aFunctions)) {
                $this->sClassContent .= $oVariable->getFunction();
                $this->aFunctions[] = $sFunctionName;
            }
        }

        unset($oVariable, $sFunctionName);
    }

    private function _useBlocks()
    {
        $aMatches = array();
        preg_match_all('/{{ (block)(\(["\'])(.*)(["\']\)) }}/U', $this->sClassContent, $aMatches, PREG_SET_ORDER);

        foreach ($aMatches as $aVariable) {
            $sBlock = '<?php $this->_' . $aVariable[3] . 'Block(); ?>';
            $this->sClassContent = str_replace($aVariable[0], $sBlock, $this->sClassContent);
        }

        unset($aMatches, $aVariable, $sBlock);
    }

    public static function phpRow($sString, $nTab = 1, $nEol = 1)
    {
        for ($n = 1; $n <= $nTab; $n++) {
            $sString = "\t" . $sString;
        }

        for ($n = 1; $n <= $nEol; $n++) {
            $sString .= "\n";
        }

        unset($nTab, $nEol);
        return $sString;
    }

}

# End of file