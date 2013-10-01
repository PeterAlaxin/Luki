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

use Luki\Template;
use Luki\Template\Variable;

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
            $this->sContent = str_replace($aMatch[0], '<?php $this->aData["' . $aMatch[1] . '"] = ' . $aMatch[2] . '; ?>', $this->sContent);
        }
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
            
            $oVariable = new Variable($aMatch[2]);            
            $this->aVariables[] = $oVariable;
            
            $sFor = Template::phpRow('<?php '); 
            $sFor .= Template::phpRow('$this->aLoop[] = $this->aData["loop"];', 2); 
            $sFor .= Template::phpRow('$this->aData["loop"]["variable"] = ' . $oVariable->getVariable() . ';', 2); 
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
        
        unset($aMatches, $aMatch, $oVariable, $sFor, $sEndFor);
    }

    private function _ifBlock()
    {
        $aMatches = array();
        preg_match_all('/{% if (.+\w) %}/U', $this->sContent, $aMatches, PREG_SET_ORDER);

        foreach($aMatches as $aMatch) {
            $aSubMatches = array();
            preg_match_all('/\(|\)|==|===|!=|\<|\>|\<=|\>=|[a-z0-9_]*|\+|-/', $aMatch[1], $aSubMatches, PREG_SET_ORDER);

            $sCondition = '';
            
            foreach($aSubMatches as $aSubMatch) {
                if(in_array($aSubMatch[0], $this->aOperators) or is_numeric($aSubMatch[0])) {
                    $sCondition .= $aSubMatch[0]; 
                }
                elseif(empty($aSubMatch[0])) {
                    $sCondition .= ' ';
                }
                else {
                     $oVariable = new Variable($aSubMatch[0]);            
                     $this->aVariables[] = $oVariable;
                     $sCondition .= $oVariable->getVariable();
                }
            }            
        
            $this->sContent = str_replace($aMatch[0], Template::phpRow('<?php if(' . trim($sCondition) . ') { ?>'), $this->sContent);
        }        
        
        $this->sContent = str_replace('{% else %}', Template::phpRow('<?php } else { ?>'), $this->sContent);
        $this->sContent = str_replace('{% endif %}', Template::phpRow('<?php } ?>'), $this->sContent);
        
        unset($aMatch, $aMatches);
    }
}

# End of file