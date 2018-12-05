<?php
/**
 * Template Block class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Template
 * @filesource
 */

namespace Luki\Template;

use Luki\Loader,
    Luki\Template,
    Luki\Template\Variable;

class Block
{
    protected $block;
    protected $content            = '';
    protected $transformedContent = '';
    protected $variables          = array();
    protected $operators          = array("(", ")", "==", "===", "!=", "<", ">", "<=", ">=", "+", "-", "or", "and");
    protected $logic              = array("is blank", "is not blank", "is constant", "is not constant", "is defined", "is not defined",
        "is even", "is not even", "is iterable", "is not iterable", "is null", "is not null", "is odd", "is not odd", "is sameas",
        "is not sameas", "is divisible by", "is not divisible by");

    public function __construct($block)
    {
        $this->block = $block;

        if (is_array($block)) {
            $this->content = $block[2];
        } else {
            $this->content = $block;
        }

        $this->defineVariables();
        $this->setVariables();
        $this->forBlock();
        $this->ifBlock();
        $this->includeBlock();
        $this->renderBlock();
        $this->transformVariables();
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function getContent()
    {
        return $this->transformedContent;
    }

    public function getVariables()
    {
        return $this->variables;
    }

    private function defineVariables()
    {
        $matches = array();
        preg_match_all('/{{ ((block)(\()(.*)(\))|(.*)) }}/U', $this->content, $matches, PREG_SET_ORDER);

        foreach ($matches as $variable) {
            if (empty($variable[2])) {
                $this->variables[] = new Variable($variable[1]);
            }
        }
    }

    private function setVariables()
    {
        $matches = array();
        preg_match_all('|{% set (.*) = (.*) %}|U', $this->content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $text = '<?php $this->aData["'.$match[1].'"] = ';

            $subMatches = array();
            preg_match_all('/[a-zA-Z0-9_\."\'\(\),\|]*|\+|-|\*|\/|\(|\)/', $match[2], $subMatches, PREG_SET_ORDER);

            foreach ($subMatches as $subMatch) {
                if ('' === $subMatch[0]) {
                    continue;
                }
                if (in_array($subMatch[0], array('+', '-', '*', '/', '(', ')'))) {
                    $text .= $subMatch[0];
                } else {
                    $text .= $this->transformToVariable($subMatch[0], true);
                }
            }

            $text          .= '; ?>';
            $this->content = str_replace($match[0], $text, $this->content);
        }
    }

    private function transformVariables()
    {
        $this->transformedContent = $this->content;
        foreach ($this->variables as $variable) {
            $from                     = '{{ '.$variable->getContent().' }}';
            $to                       = $variable->getCode();
            $this->transformedContent = str_replace($from, $to, $this->transformedContent);
        }
    }

    private function forBlock()
    {
        $matches = array();
        preg_match_all('|{% for (.*) in (.*) %}|U', $this->content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {

            if (strpos($match[1], ', ')) {
                list($key, $value) = explode(', ', $match[1]);
            } else {
                $key   = null;
                $value = $match[1];
            }

            if (!is_null($key)) {
                $this->variables[] = new Variable($key);
            }
            $this->variables[] = new Variable($value);
            $variable          = $this->transformToVariable($match[2], true);

            $for = Template::phpRow('<?php ');
            $for .= Template::phpRow('$this->aLoop[] = $this->aData["loop"];', 2);
            $for .= Template::phpRow('$this->aData["loop"]["variable"] = '.$variable.';', 2);
            $for .= Template::phpRow('if(empty($this->aData["loop"]["variable"])) { $this->aData["loop"]["variable"] = array(); }',
                    2);
            $for .= Template::phpRow('$this->aData["loop"]["length"] = Luki\Template\Variable::getVariableLenght($this->aData["loop"]["variable"]);',
                    2);
            $for .= Template::phpRow('$this->aData["loop"]["index"] = -1;', 2);
            $for .= Template::phpRow('$this->aData["loop"]["index1"] = 0;', 2);
            $for .= Template::phpRow('$this->aData["loop"]["revindex"] = $this->aData["loop"]["length"];', 2);
            $for .= Template::phpRow('$this->aData["loop"]["revindex1"] = $this->aData["loop"]["length"]+1;', 2);
            $for .= $this->elseFor($match);
            if (!is_null($key)) {
                $for .= Template::phpRow('foreach($this->aData["loop"]["variable"] as $this->aData["'.$key.'"] => $this->aData["'.$value.'"]) {',
                        2);
            } else {
                $for .= Template::phpRow('foreach($this->aData["loop"]["variable"] as $this->aData["'.$value.'"]) {', 2);
            }
            $for .= Template::phpRow('$this->aData["loop"]["index"]++;', 3);
            $for .= Template::phpRow('$this->aData["loop"]["index1"]++;', 3);
            $for .= Template::phpRow('$this->aData["loop"]["revindex"]--;', 3);
            $for .= Template::phpRow('$this->aData["loop"]["revindex1"]--;', 3);
            $for .= Template::phpRow('$this->aData["loop"]["first"] = $this->aData["loop"]["index"] == 0 ? true : false;',
                    3);
            $for .= Template::phpRow('$this->aData["loop"]["last"] = $this->aData["loop"]["index1"] == $this->aData["loop"]["length"] ? true : false;',
                    3);
            $for .= Template::phpRow('$this->aData["loop"]["even"] = $this->aData["loop"]["index1"]/2 == round($this->aData["loop"]["index1"]/2) ? true : false;',
                    3);
            $for .= Template::phpRow('$this->aData["loop"]["odd"] = !$this->aData["loop"]["even"]', 3);
            $for .= Template::phpRow(' ?>', 1);

            $this->content = str_replace($match[0], $for, $this->content);
        }

        $endFor        = Template::phpRow('<?php }');
        $endFor        .= '$this->aData["loop"] = array_pop($this->aLoop);';
        $endFor        .= Template::phpRow(' ?>', 1);
        $this->content = str_replace('{% endfor %}', $endFor, $this->content);
    }

    private function elseFor($match)
    {
        $matches = array();
        $elseFor = '';
        $regexp  = '|('.$match[0].')([\s\S]*)({% elsefor %})([\s\S]*)({% endfor %})|U';

        preg_match_all($regexp, $this->content, $matches, PREG_SET_ORDER);

        if (!empty($matches)) {
            $elseFor .= Template::phpRow('if(0 == $this->aData["loop"]["length"]) {', 2);
            $elseFor .= Template::phpRow(' ?>', 1);
            $elseFor .= $matches[0][4];
            $elseFor .= Template::phpRow('<?php ');
            $elseFor .= Template::phpRow('}', 2);

            $this->content = str_replace($matches[0][3].$matches[0][4], '', $this->content);
        }

        return $elseFor;
    }

    private function ifBlock()
    {
        $matches = array();
        preg_match_all('|{% if (.+) %}|U', $this->content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {

            $subMatches = array();
            preg_match_all('/\(|\)|is blank|is not blank|is constant|is not constant|is defined|is not defined|is even|is not even|is iterable|is not iterable|is null|is not null|is odd|is not odd|is sameas|is not sameas|is divisible by|is not divisible by|==|===|!=|\<|\>|\<=|\>=|".*"|[a-zA-Z0-9_\."\']*|\+|-/',
                $match[1], $subMatches, PREG_SET_ORDER);
            $condition  = '';

            foreach ($subMatches as $subMatch) {
                if (in_array($subMatch[0], $this->operators) or is_numeric($subMatch[0])) {
                    $condition .= $subMatch[0];
                } elseif (in_array($subMatch[0], $this->logic)) {
                    $condition = $this->generateTest($match, $subMatch);
                } elseif (empty($subMatch[0])) {
                    $condition .= ' ';
                } else {
                    $condition .= $this->transformToVariable($subMatch[0], true);
                }

                if (strpos($subMatch[0], ' sameas') > 0 or
                    strpos($subMatch[0], ' divisible by') > 0) {
                    break;
                }
            }

            $this->content = str_replace($match[0], Template::phpRow('<?php if('.trim($condition).') { ?>', 0, 0),
                $this->content);
        }

        $this->content = str_replace('{% else %}', Template::phpRow('<?php } else { ?>', 0, 0), $this->content);
        $this->content = str_replace('{% endif %}', Template::phpRow('<?php } ?>', 0, 0), $this->content);
    }

    private function includeBlock()
    {
        $matches = array();
        preg_match_all('|{% include (.+) with (.+) %}|U', $this->content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $template = $this->transformToTemplate($match[1]);
            $variable = $this->transformToVariable($match[2]);

            $include = Template::phpRow('<?php ');
            $include .= Template::phpRow('$oTemplate = new Luki\Template("'.$template.'", '.$variable.');', 2);
            $include .= Template::phpRow('echo $oTemplate->Render();', 2);
            $include .= Template::phpRow(' ?>', 1);

            $this->content = str_replace($match[0], $include, $this->content);
        }

        $matches = array();
        preg_match_all('|{% include (.+) %}|U', $this->content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $template = $this->transformToTemplate($match[1]);

            $include = Template::phpRow('<?php ');
            $include .= Template::phpRow('$oTemplate = new Luki\Template("'.$template.'");', 2);
            $include .= Template::phpRow('echo $oTemplate->Render();', 2);
            $include .= Template::phpRow(' ?>', 1);

            $this->content = str_replace($match[0], $include, $this->content);
        }
    }

    private function renderBlock()
    {
        $matches = array();
        preg_match_all('|{% render (.+) with (.+) %}|U', $this->content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $controller = $this->transformToController($match[1]);
            $variable   = $this->transformToVariable($match[2]);

            $include = Template::phpRow('<?php ');
            $include .= Template::phpRow('$oController = new '.$controller['controller'].'('.$variable.');', 2);
            $include .= Template::phpRow('$oController->preDispatch();', 2);
            $include .= Template::phpRow('$oController->'.$controller['action'].'();', 2);
            $include .= Template::phpRow('$oController->postDispatch();', 2);
            $include .= Template::phpRow('echo $oController->getOutput();', 2);
            $include .= Template::phpRow(' ?>', 1);

            $this->content = str_replace($match[0], $include, $this->content);
        }

        $matches = array();
        preg_match_all('|{% render (.+) %}|U', $this->content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $controller = $this->transformToController($match[1]);

            $include = Template::phpRow('<?php ');
            $include .= Template::phpRow('$oController = new '.$controller['controller'].'();', 2);
            $include .= Template::phpRow('$oController->preDispatch();', 2);
            $include .= Template::phpRow('$oController->'.$controller['action'].'();', 2);
            $include .= Template::phpRow('$oController->postDispatch();', 2);
            $include .= Template::phpRow('echo $oController->getOutput();', 2);
            $include .= Template::phpRow(' ?>', 1);

            $this->content = str_replace($match[0], $include, $this->content);
        }
    }

    private function transformToController($controller)
    {
        $controller = str_replace(array('"', "'"), array('', ''), $controller);
        $path       = explode(':', $controller);

        if (empty($path[2])) {
            $path[2] = 'index';
        }

        $newController = array(
            'controller' => '\\'.$path[0].'\\'.$path[1],
            'action'     => $path[2].'Action'
        );

        return $newController;
    }

    private function transformToTemplate($template)
    {
        $templates = explode('/', $template);

        foreach ($templates as $key => $item) {
            $templates[$key] = str_replace(array('"', "'"), array('', ''), $item);
        }

        if (empty($templates[2])) {
            $transformedTemplate = Loader::isFile($templates[0].'/template/'.$templates[1]);
        } else {
            $transformedTemplate = Loader::isFile($templates[0].'/template/'.$templates[1].'/'.$templates[2]);
        }

        return $transformedTemplate;
    }

    private function transformToVariable($variableName, $addToVariables = false)
    {
        $variable     = new Variable($variableName);
        $variableName = $variable->getVariable();

        if ($addToVariables) {
            $this->variables[] = $variable;
        }

        return $variableName;
    }

    private function generateTest($match, $subMatch)
    {
        $condition = '';
        $test      = str_replace('is ', '', str_replace('is not ', '', $subMatch[0]));

        switch ($test) {
            case 'blank':
            case 'constant':
            case 'defined':
            case 'even':
            case 'iterable':
            case 'null':
            case 'odd':
                $conditions = explode(' ', $match[1]);
                $variable   = $this->transformToVariable($conditions[0]);
                $condition  .= '$this->aTests["'.$test.'"]->Is('.$variable.')';
                break;
            case 'sameas':
                $conditions = explode(' ', $match[1]);
                $first      = $this->transformToVariable($conditions[0]);
                $second     = $this->transformToVariable($conditions[count($conditions) - 1]);
                $condition  .= '$this->aTests["'.$test.'"]->Is('.$first.', '.$second.')';
                break;
            case 'divisible by':
                $conditions = explode(' ', $match[1]);
                $first      = $this->transformToVariable($conditions[0]);
                $second     = str_replace(array("by(", ")"), array("", ""), $conditions[count($conditions) - 1]);
                $condition  .= '$this->aTests["divisibleby"]->Is('.$first.', '.$second.')';
                break;
        }

        if (strpos($subMatch[0], ' not ') > 0) {
            $condition = '!'.$condition;
        }

        return $condition;
    }
}