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
class Block
{

    protected $_block;
    protected $_content = '';
    protected $_transformedContent = '';
    protected $_variables = array();
    protected $_operators = array( "(", ")", "==", "===", "!=", "<", ">", "<=", ">=", "+", "-", "or", "and" );
    protected $_logic = array( "is blank", "is not blank", "is constant", "is not constant", "is defined", "is not defined", 
      "is even", "is not even", "is iterable", "is not iterable", "is null", "is not null", "is odd", "is not odd", "is sameas", 
      "is not sameas", "is divisible by", "is not divisible by");

    public function __construct($block)
    {
        $this->_block = $block;

        if ( is_array($block) ) {
            $this->_content = $block[2];
        } else {
            $this->_content = $block;
        }

        $this->_defineVariables();
        $this->_setVariables();
        $this->_forBlock();
        $this->_ifBlock();
        $this->_includeBlock();
        $this->_transformVariables();

        unset($block);
    }

    public function getContent()
    {
        return $this->_transformedContent;
    }

    public function getVariables()
    {
        return $this->_variables;
    }

    private function _defineVariables()
    {
        $matches = array();
        preg_match_all('/{{ ((block)(\()(.*)(\))|(.*)) }}/U', $this->_content, $matches, PREG_SET_ORDER);

        foreach ( $matches as $variable ) {
            if ( empty($variable[2]) ) {
                $this->_variables[] = new Variable($variable[1]);
            }
        }

        unset($matches, $variable);
    }

    private function _setVariables()
    {
        $matches = array();
        preg_match_all('|{% set (.*) = (.*) %}|U', $this->_content, $matches, PREG_SET_ORDER);

        foreach ( $matches as $match ) {
            $text = '<?php $this->aData["' . $match[1] . '"] = ';

            $subMatches = array();
            preg_match_all('/[a-zA-Z0-9_\."\'\(\),\|]*|\+|-|\*|\/|\(|\)/', $match[2], $subMatches, PREG_SET_ORDER);            
     
            foreach($subMatches as $subMatch) {
                if('' === $subMatch[0]) {
                    continue;
                }
                if(in_array($subMatch[0], array('+', '-', '*', '/', '(', ')'))) {
                    $text .= $subMatch[0];
                }
                else {
                    $text .=  $this->_transformToVariable($subMatch[0], TRUE);
                }
            }
            
            $text .= '; ?>';
            $this->_content = str_replace($match[0], $text, $this->_content);
        }

        unset($matches, $match, $text);
    }

    private function _transformVariables()
    {
        $this->_transformedContent = $this->_content;
        foreach ( $this->_variables as $variable ) {
            $from = '{{ ' . $variable->getContent() . ' }}';
            $to = $variable->getCode();
            $this->_transformedContent = str_replace($from, $to, $this->_transformedContent);
        }

        unset($variable, $from, $to);
    }

    private function _forBlock()
    {
        $matches = array();
        preg_match_all('|{% for (.*) in (.*) %}|U', $this->_content, $matches, PREG_SET_ORDER);

        foreach ( $matches as $match ) {

            if(strpos($match[1], ', ')) {
                list($key, $value) = explode(', ', $match[1]);
            }
            else {
                $key = NULL;
                $value = $match[1];
            }

            if(!is_null($key)) {
                $this->_variables[] = new Variable($key);            
            }
            $this->_variables[] = new Variable($value);
            $variable = $this->_transformToVariable($match[2], TRUE);

            $for = Template::phpRow('<?php ');
            $for .= Template::phpRow('$this->aLoop[] = $this->aData["loop"];', 2);
            $for .= Template::phpRow('$this->aData["loop"]["variable"] = ' . $variable . ';', 2);
            $for .= Template::phpRow('if(empty($this->aData["loop"]["variable"])) { $this->aData["loop"]["variable"] = array(); }', 2);
            $for .= Template::phpRow('$this->aData["loop"]["length"] = Luki\Template\Variable::getVariableLenght($this->aData["loop"]["variable"]);', 2);
            $for .= Template::phpRow('$this->aData["loop"]["index"] = -1;', 2);
            $for .= Template::phpRow('$this->aData["loop"]["index1"] = 0;', 2);
            $for .= Template::phpRow('$this->aData["loop"]["revindex"] = $this->aData["loop"]["length"];', 2);
            $for .= Template::phpRow('$this->aData["loop"]["revindex1"] = $this->aData["loop"]["length"]+1;', 2);
            $for .= $this->_elseFor($match);
            if(!is_null($key)) {
                $for .= Template::phpRow('foreach($this->aData["loop"]["variable"] as $this->aData["' . $key . '"] => $this->aData["' . $value . '"]) {', 2);
            } else {
                $for .= Template::phpRow('foreach($this->aData["loop"]["variable"] as $this->aData["' . $value . '"]) {', 2);
            }
            $for .= Template::phpRow('$this->aData["loop"]["index"]++;', 3);
            $for .= Template::phpRow('$this->aData["loop"]["index1"]++;', 3);
            $for .= Template::phpRow('$this->aData["loop"]["revindex"]--;', 3);
            $for .= Template::phpRow('$this->aData["loop"]["revindex1"]--;', 3);
            $for .= Template::phpRow('$this->aData["loop"]["first"] = $this->aData["loop"]["index"] == 0 ? TRUE : FALSE;', 3);
            $for .= Template::phpRow('$this->aData["loop"]["last"] = $this->aData["loop"]["index1"] == $this->aData["loop"]["length"] ? TRUE : FALSE;', 3);
            $for .= Template::phpRow('$this->aData["loop"]["even"] = $this->aData["loop"]["index1"]/2 == round($this->aData["loop"]["index1"]/2) ? TRUE : FALSE;', 3);
            $for .= Template::phpRow('$this->aData["loop"]["odd"] = !$this->aData["loop"]["even"]', 3);
            $for .= Template::phpRow(' ?>', 1);

            $this->_content = str_replace($match[0], $for, $this->_content);
        }

        $endFor = Template::phpRow('<?php }');
        $endFor .= '$this->aData["loop"] = array_pop($this->aLoop);';
        $endFor .= Template::phpRow(' ?>', 1);
        $this->_content = str_replace('{% endfor %}', $endFor, $this->_content);

        unset($matches, $match, $for, $endFor, $variable);
    }

    private function _elseFor($match)
    {
        $matches = array();
        $elseFor = '';
        $regexp = '|(' . $match[0] . ')([\s\S]*)({% elsefor %})([\s\S]*)({% endfor %})|U';
        
        preg_match_all($regexp, $this->_content, $matches, PREG_SET_ORDER);

        if ( !empty($matches) ) {
            $elseFor .= Template::phpRow('if(0 == $this->aData["loop"]["length"]) {', 2);
            $elseFor .= Template::phpRow(' ?>', 1);
            $elseFor .= $matches[0][4];
            $elseFor .= Template::phpRow('<?php ');
            $elseFor .= Template::phpRow('}', 2);

            $this->_content = str_replace($matches[0][3] . $matches[0][4], '', $this->_content);
        }

        unset($match, $matches, $regexp);
        return $elseFor;
    }

    private function _ifBlock()
    {
        $matches = array();
        preg_match_all('|{% if (.+) %}|U', $this->_content, $matches, PREG_SET_ORDER);

        foreach ( $matches as $match ) {

            $subMatches = array();
            preg_match_all('/\(|\)|is blank|is not blank|is constant|is not constant|is defined|is not defined|is even|is not even|is iterable|is not iterable|is null|is not null|is odd|is not odd|is sameas|is not sameas|is divisible by|is not divisible by|==|===|!=|\<|\>|\<=|\>=|".*"|[a-zA-Z0-9_\."\']*|\+|-/', $match[1], $subMatches, PREG_SET_ORDER);
            $condition = '';

            foreach ( $subMatches as $subMatch ) {
                if ( in_array($subMatch[0], $this->_operators) or is_numeric($subMatch[0]) ) {
                    $condition .= $subMatch[0];
                } elseif ( in_array($subMatch[0], $this->_logic) ) {
                    $condition = $this->_generateTest($match, $subMatch);
                } elseif ( empty($subMatch[0]) ) {
                    $condition .= ' ';
                } else {
                    $condition .= $this->_transformToVariable($subMatch[0], TRUE);
                }
                
                if(strpos($subMatch[0], ' sameas') > 0 or
                   strpos($subMatch[0], ' divisible by') > 0) {
                    break;
                }
            }

            $this->_content = str_replace($match[0], Template::phpRow('<?php if(' . trim($condition) . ') { ?>', 0, 0), $this->_content);
        }

        $this->_content = str_replace('{% else %}', Template::phpRow('<?php } else { ?>', 0, 0), $this->_content);
        $this->_content = str_replace('{% endif %}', Template::phpRow('<?php } ?>', 0, 0), $this->_content);

        unset($match, $matches, $subMatches, $subMatch, $condition);
    }

    private function _includeBlock()
    {
        $matches = array();
        preg_match_all('|{% include (.+) with (.+) %}|U', $this->_content, $matches, PREG_SET_ORDER);

        foreach ( $matches as $match ) {
            $template = $this->_transformToTemplate($match[1]);
            $variable = $this->_transformToVariable($match[2]);

            $include = Template::phpRow('<?php ');
            $include .= Template::phpRow('$oTemplate = new Luki\Template("' . $template . '", ' . $variable . ');', 2);
            $include .= Template::phpRow('echo $oTemplate->Render();', 2);
            $include .= Template::phpRow(' ?>', 1);

            $this->_content = str_replace($match[0], $include, $this->_content);
        }

        $matches = array();
        preg_match_all('|{% include (.+) %}|U', $this->_content, $matches, PREG_SET_ORDER);

        foreach ( $matches as $match ) {
            $template = $this->_transformToTemplate($match[1]);

            $include = Template::phpRow('<?php ');
            $include .= Template::phpRow('$oTemplate = new Luki\Template("' . $template . '");', 2);
            $include .= Template::phpRow('echo $oTemplate->Render();', 2);
            $include .= Template::phpRow(' ?>', 1);

            $this->_content = str_replace($match[0], $include, $this->_content);
        }

        unset($matches, $match, $include, $template, $variable);
    }

    private function _transformToTemplate($template)
    {
        $templates = explode('/', $template);
        $newTemplate = array();

        foreach ( $templates as $key => $item ) {
            if ( $key + 1 == count($templates) ) {
                $newTemplate[] = 'template';
            }
            $newTemplate[] = $item;
        }

        $template = str_replace(array( '"', "'" ), array( '', '' ), implode('/', $newTemplate));
        $transformedTemplate = Loader::isFile($template);

        unset($template, $templates, $newTemplate, $key, $item);
        return $transformedTemplate;
    }

    private function _transformToVariable($variableName, $addToVariables = FALSE)
    {
        $variable = new Variable($variableName);
        $variableName = $variable->getVariable();

        if ( $addToVariables ) {
            $this->_variables[] = $variable;
        }

        unset($variable, $addToVariables);
        return $variableName;
    }

    private function _generateTest($match, $subMatch)
    {
        $condition = '';
        $test = str_replace('is ', '', str_replace('is not ', '', $subMatch[0]));

        switch ( $test ) {
            case 'blank':
            case 'constant':
            case 'defined':
            case 'even':
            case 'iterable':
            case 'null':
            case 'odd':
                $conditions = explode(' ', $match[1]);
                $variable = $this->_transformToVariable($conditions[0]);
                $condition .= '$this->aTests["' . $test . '"]->Is(' . $variable . ')';
                break;
            case 'sameas':
                $conditions = explode(' ', $match[1]);
                $first = $this->_transformToVariable($conditions[0]);
                $second = $this->_transformToVariable($conditions[count($conditions)-1]); 
                $condition .= '$this->aTests["' . $test . '"]->Is(' . $first . ', ' . $second .')';
                break;
            case 'divisible by':
                $conditions = explode(' ', $match[1]);
                $first = $this->_transformToVariable($conditions[0]);
                $second = str_replace(array("by(", ")"), array("", ""), $conditions[count($conditions)-1]);
                $condition .= '$this->aTests["divisibleby"]->Is(' . $first . ', ' . $second .')';
                break;
        }

        if ( strpos($subMatch[0], ' not ') > 0 ) {
            $condition = '!' . $condition;
        }

        unset($match, $subMatch, $test, $conditions, $variable);
        return $condition;
    }

}

# End of file