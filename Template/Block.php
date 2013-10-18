<?php

/**
 * Template Block class
 *
 * Luki framework
 * Date 7.4.2013
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

use Luki\Loader,
    Luki\Template,
    Luki\Template\Variable;

/**
 * Template Block class
 *
 * @package Luki
 */
class Block {

    protected $aBlock;
    protected $sContent = '';
    protected $sTransformedContent = '';
    protected $aVariables = array();
    protected $aOperators = array("(", ")", "==", "===", "!=", "<", ">", "<=", ">=", "+", "-", "or", "and");

    public function __construct($Block)
    {
        $this->aBlock = $Block;

        if(is_array($Block)) {
            $this->sContent = $Block[2];
        }
        else {
            $this->sContent = $Block;
        }

        $this->_defineVariables();
        $this->_setVariables();
        $this->_forBlock();
        $this->_ifBlock();
        $this->_includeBlock();
        $this->_transformVariables();
                
        unset($Block);
    }

    public function getContent()
    {
        return $this->sTransformedContent;
    }

    public function getVariables()
    {
        return $this->aVariables;
    }

    private function _defineVariables()
    {
        $aMatches = array();
        preg_match_all('/{{ ((block)(\()(.*)(\))|(.*)) }}/U', $this->sContent, $aMatches, PREG_SET_ORDER);

        foreach ($aMatches as $aVariable) {
            if(empty($aVariable[2])) {
                $this->aVariables[] = new Variable($aVariable[1]);
            }
        }

        unset($aMatches, $aVariable);
    }

    private function _setVariables()
    {
        $aMatches = array();
        preg_match_all('|{% set (.*) = (.*) %}|U', $this->sContent, $aMatches, PREG_SET_ORDER);

        foreach($aMatches as $aMatch) {
            $this->sContent = str_replace($aMatch[0], '<?php $this->aData["' . $aMatch[1] . '"] = ' . $this->_transformToVariable($aMatch[2]) . '; ?>', $this->sContent);
        }
        
        unset($aMatches, $aMatch);
    }

    private function _transformVariables()
    {
        $this->sTransformedContent = $this->sContent;
        foreach ($this->aVariables as $oVariable) {
            $this->sTransformedContent = str_replace('{{ ' . $oVariable->getContent() . ' }}', $oVariable->getCode(), $this->sTransformedContent);
        }

        unset($oVariable);
    }

    private function _forBlock()
    {
        $aMatches = array();
        preg_match_all('|{% for (.*) in (.*) %}|U', $this->sContent, $aMatches, PREG_SET_ORDER);

        foreach($aMatches as $aMatch) {
            $this->aVariables[] = new Variable($aMatch[1]);
            $sVariable = $this->_transformToVariable($aMatch[2], TRUE);
            
            $sFor = Template::phpRow('<?php '); 
            $sFor .= Template::phpRow('if(empty(' . $sVariable . ')) { ' . $sVariable . ' = array(); }', 2); 
            $sFor .= Template::phpRow('$this->aLoop[] = $this->aData["loop"];', 2); 
            $sFor .= Template::phpRow('$this->aData["loop"]["variable"] = ' . $sVariable . ';', 2); 
            $sFor .= Template::phpRow('$this->aData["loop"]["length"] = count($this->aData["loop"]["variable"]);', 2); 
            $sFor .= Template::phpRow('$this->aData["loop"]["index"] = -1;', 2); 
            $sFor .= Template::phpRow('$this->aData["loop"]["index1"] = 0;', 2); 
            $sFor .= Template::phpRow('$this->aData["loop"]["revindex"] = $this->aData["loop"]["length"];', 2); 
            $sFor .= Template::phpRow('$this->aData["loop"]["revindex1"] = $this->aData["loop"]["length"]+1;', 2); 
            $sFor .= Template::phpRow('foreach($this->aData["loop"]["variable"] as $this->aData["' . $aMatch[1] . '"]) {', 2);
            $sFor .= Template::phpRow('$this->aData["loop"]["index"]++;', 3);
            $sFor .= Template::phpRow('$this->aData["loop"]["index1"]++;', 3);
            $sFor .= Template::phpRow('$this->aData["loop"]["revindex"]--;', 3);
            $sFor .= Template::phpRow('$this->aData["loop"]["revindex1"]--;', 3);
            $sFor .= Template::phpRow('$this->aData["loop"]["first"] = $this->aData["loop"]["index"] == 0 ? TRUE : FALSE;', 3);
            $sFor .= Template::phpRow('$this->aData["loop"]["last"] = $this->aData["loop"]["index1"] == $this->aData["loop"]["length"] ? TRUE : FALSE;', 3);
            $sFor .= Template::phpRow('$this->aData["loop"]["even"] = $this->aData["loop"]["index1"]/2 == round($this->aData["loop"]["index1"]/2) ? TRUE : FALSE;', 3);
            $sFor .= Template::phpRow('$this->aData["loop"]["odd"] = !$this->aData["loop"]["even"]', 3);
            $sFor .= Template::phpRow(' ?>', 1); 
            
            $this->sContent = str_replace($aMatch[0], $sFor, $this->sContent);
        }
        
        $sEndFor = Template::phpRow('<?php }'); 
        $sEndFor .= '$this->aData["loop"] = array_pop($this->aLoop);';
        $sEndFor .= Template::phpRow(' ?>', 1); 
        $this->sContent = str_replace('{% endfor %}', $sEndFor, $this->sContent);
        
        unset($aMatches, $aMatch, $sFor, $sEndFor, $sVariable);
    }

    private function _ifBlock()
    {
        $aMatches = array();
        preg_match_all('|{% if (.+) %}|U', $this->sContent, $aMatches, PREG_SET_ORDER);

        foreach($aMatches as $aMatch) {

            $aSubMatches = array();
            preg_match_all('/\(|\)|==|===|!=|\<|\>|\<=|\>=|[a-z0-9_\."\']*|\+|-/', $aMatch[1], $aSubMatches, PREG_SET_ORDER);

            $sCondition = '';
            
            foreach($aSubMatches as $aSubMatch) {
                if(in_array($aSubMatch[0], $this->aOperators) or is_numeric($aSubMatch[0])) {
                    $sCondition .= $aSubMatch[0]; 
                }
                elseif(empty($aSubMatch[0])) {
                    $sCondition .= ' ';
                }
                else {
                     $sCondition .= $this->_transformToVariable($aSubMatch[0], TRUE);
                }
            }            
        
            $this->sContent = str_replace($aMatch[0], Template::phpRow('<?php if(' . trim($sCondition) . ') { ?>', 0, 0), $this->sContent);
        }        
        
        $this->sContent = str_replace('{% else %}', Template::phpRow('<?php } else { ?>', 0, 0), $this->sContent);
        $this->sContent = str_replace('{% endif %}', Template::phpRow('<?php } ?>', 0, 0), $this->sContent);
        
        unset($aMatch, $aMatches, $aSubMatches, $aSubMatch, $sCondition);
    }

    private function _includeBlock()
    {
        $aMatches = array();
        preg_match_all('|{% include (.+) with (.+) %}|U', $this->sContent, $aMatches, PREG_SET_ORDER);
        
        foreach($aMatches as $aMatch) {
            $sTemplate = $this->_transformToTemplate($aMatch[1]);
            $sVariable = $this->_transformToVariable($aMatch[2]);
            
            $sInclude = Template::phpRow('<?php '); 
            $sInclude .= Template::phpRow('$oTemplate = new Luki\Template("' . $sTemplate . '", ' . $sVariable . ');', 2);
            $sInclude .= Template::phpRow('echo $oTemplate->Render();', 2);
            $sInclude .= Template::phpRow(' ?>', 1); 
        
            $this->sContent = str_replace($aMatch[0], $sInclude, $this->sContent);
        }
        
        $aMatches = array();
        preg_match_all('|{% include (.+) %}|U', $this->sContent, $aMatches, PREG_SET_ORDER);
        
        foreach($aMatches as $aMatch) {
            $sTemplate = $this->_transformToTemplate($aMatch[1]);

            $sInclude = Template::phpRow('<?php '); 
            $sInclude .= Template::phpRow('$oTemplate = new Luki\Template("' . $sTemplate . '");', 2);
            $sInclude .= Template::phpRow('echo $oTemplate->Render();', 2);
            $sInclude .= Template::phpRow(' ?>', 1); 
        
            $this->sContent = str_replace($aMatch[0], $sInclude, $this->sContent);
        }
        
        unset($aMatches, $aMatch, $sInclude, $sTemplate, $sVariable);
    }
    
    private function _transformToTemplate($sTemplate)
    {
        $aTemplate = explode('/', $sTemplate);
        $aNewTemplate = array();

        foreach($aTemplate as $nKey => $sItem) {
            if($nKey+1 == count($aTemplate)) {
                $aNewTemplate[] = 'template';
            }
            $aNewTemplate[] = $sItem;
        }

        $sTemplate = str_replace(array('"', "'"), array('', ''), implode('/', $aNewTemplate));
        $sReturn = Loader::isFile($sTemplate);
        
        unset($sTemplate, $aTemplate, $aNewTemplate, $nKey, $sItem);
        return $sReturn;
    }
    
    private function _transformToVariable($sVariable, $bAddToVariables = FALSE)
    {
        $oVariable = new Variable($sVariable);
        $sVariable = $oVariable->getVariable();
        
        if($bAddToVariables) {
            $this->aVariables[] = $oVariable;
        }
        
        unset($oVariable, $bAddToVariables);
        return $sVariable;
    }
}

# End of file