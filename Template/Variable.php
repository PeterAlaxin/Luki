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
class Variable
{

    protected $_content = '';
    protected $_variable = '';
    protected $_functionName = '';
    protected $_function = '';
    protected $_transformedVariable = '';
    protected $_code = '';
    protected $_filters = array();
    protected $_finalVariable = '';

    public function __construct($content)
    {
        $this->_content = $content;

        $this->_prepareFilters();
        $this->_transformVariable();
        $this->_prepareVariable();

        unset($content);
    }

    public function getCode()
    {
        return $this->_code;
    }

    public function getContent()
    {
        return $this->_content;
    }

    public function getFunctionName()
    {
        return $this->_functionName;
    }

    public function getFunction()
    {
        $this->_prepareFunction();

        return $this->_function;
    }

    public function getVariable()
    {
        return $this->_finalVariable;
    }

    public static function getVariableLenght($variable)
    {

        switch ( gettype($variable) ) {
            case 'array':
            case 'boolean':
            case 'NULL':
                $lenght = count($variable);
                break;
            case 'string':
                $lenght = strlen($variable);
                break;
            case 'integer':
            case 'double':
                $lenght = $variable;
                break;
            case 'object':
                if ( is_a($variable, 'Luki\Data\MySQL\Result') ) {
                    $lenght = $variable->getNumberOfRecords();
                }
                break;
            default :
                $lenght = NULL;
        }

        unset($variable);
        return $lenght;
    }

    private function _prepareFilters()
    {
        if ( strpos($this->_content, '|') ) {
            $this->_filters = explode('|', $this->_content);
            $this->_variable = array_shift($this->_filters);
            $this->_functionName = 'fnc_' . sha1($this->_content);
        } else {
            $this->_variable = $this->_content;
        }
    }

    private function _transformVariable()
    {
        $matches = array();
        $hasKeys = FALSE;
        preg_match('/^[\[{](.*)[\]}]$/', $this->_variable, $matches);

        if ( count($matches) > 0 ) {
            $newItems = array();
            $items = explode(', ', $matches[1]);

            foreach ( $items as $item ) {
                if ( strpos($item, ': ') ) {
                    $hasKeys = TRUE;
                    $subItems = explode(': ', $item);

                    $newItems[] = $this->_stringToVariable($subItems[0]) .
                            ' => ' .
                            $this->_stringToVariable($subItems[1]);
                } else {
                    $newItems[] = $this->_stringToVariable($item);
                }
            }

            $variable = preg_replace('/[\[{]/', 'array(', $this->_variable);
            $variables = preg_replace('/' . $matches[1] . '/', implode(', ', $newItems), $variable);
            if ( $hasKeys ) {
                $this->_transformedVariable = preg_replace('/[}]/', ')', $variables);
            } else {
                $this->_transformedVariable = preg_replace('/[\]}]/', ')', $variables);
            }
        } else {
            $this->_transformedVariable = $this->_stringToVariable($this->_variable);
        }

        unset($matches, $newItems, $items, $item, $subItems, $variable, $variables, $hasKeys);
    }

    private function _stringToVariable($string)
    {
        $types = array( 'RangeOperator' => '/^(.*)\.\.(.*)$/',
          'SubArray' => '/^(.*)\.(.*)$/',
          'Range' => '/^range\((.*)\)$/',
          'Random' => '/^random\((.*)\)$/',
          'Constant' => '/^constant\((.*)\)$/'
        );
        $formatedString = '';

        if ( !preg_match('/^[\'"]/', $string) and ! is_numeric($string) ) {

            foreach ( $types as $type => $regexp ) {
                $matches = array();
                preg_match($regexp, $this->_variable, $matches);

                if ( !empty($matches) ) {
                    switch ( $type ) {
                        case 'Random':
                            $formatedString = '$this->aFunctions["random"]->Get(' . $matches[1] . ')';
                            break;
                        case 'Range':
                            $formatedString = '$this->aFunctions["range"]->Get(' . $matches[1] . ')';
                            break;
                        case 'Constant':
                            $formatedString = '$this->aFunctions["constant"]->Get(' . $matches[1] . ')';
                            break;
                        case 'SubArray':
                            $items = explode('.', $matches[0]);
                            $formatedString = '$this->aData["' . implode('"]["', $items) . '"]';
                            break;
                        case 'RangeOperator':
                            $range = explode('..', $string);
                            $newArray = array();
                            if ( is_numeric($range[0]) ) {
                                $min = min($range[0], $range[1]);
                                $max = max($range[0], $range[1]);
                                for ( $i = $min; $i <= $max; $i++ ) {
                                    $newArray[] = $i;
                                }
                                $formatedString = 'array(' . implode(',', $newArray) . ')';
                            } else {
                                $min = min(ord($range[0]), ord($range[1]));
                                $max = max(ord($range[0]), ord($range[1]));
                                for ( $i = $min; $i <= $max; $i++ ) {
                                    $newArray[] = chr($i);
                                }
                                $formatedString = 'array("' . implode('","', $newArray) . '")';
                            }
                            break;
                    }

                    break;
                }
            }

            if ( empty($formatedString) ) {
                $formatedString = '$this->aData["' . $string . '"]';
            }
        } else {
            $formatedString = $string;
        }

        unset($matches, $range, $string, $type, $regexp, $items);
        return $formatedString;
    }

    private function _prepareVariable()
    {
        if ( !empty($this->_filters) ) {
            $this->_finalVariable = '$this->_' . $this->_functionName . '(' . $this->_transformedVariable . ')';
        } else {
            $this->_finalVariable = $this->_transformedVariable;
        }

        $this->_code = '<?php echo ' . $this->_finalVariable . '; ?>';
    }

    private function _prepareFunction()
    {
        $matches = array();

        $function = Template::phpRow('public function _' . $this->_functionName . '($xValue)');
        $function .= Template::phpRow('{');
        foreach ( $this->_filters as $filter ) {

            preg_match_all('|(.*)\((.*)\)|U', $filter, $matches, PREG_SET_ORDER);

            if ( empty($matches) ) {
                $function .= Template::phpRow('$xValue = $this->aFilters["' . $filter . '"]->Get($xValue);', 2);
            } else {
                if ( empty($matches[0][2]) ) {
                    $text = '$xValue = $this->aFilters["' . $matches[0][1] . '"]->Get($xValue);';
                    $function .= Template::phpRow($text, 2);
                } else {
                    $parameters = $matches[0][2];
                    $subMatches = array();
                    preg_match('/^[\[{](.*)[\]}]$/', $parameters, $subMatches);

                    if ( count($subMatches) > 0 ) {
                        $parameters = preg_replace('/[\[{]/', 'array(', $parameters);
                        $parameters = preg_replace('/: /', ' => ', $parameters);
                        $parameters = preg_replace('/[\]}]/', ')', $parameters);
                    }

                    $text = '$xValue = $this->aFilters["' . $matches[0][1] . '"]->Get($xValue, ' . $parameters . ');';
                    $function .= Template::phpRow($text, 2);
                }
            }
        }
        $function .= Template::phpRow('return $xValue;', 2);
        $function .= Template::phpRow('}', 1, 2);

        $this->_function = $function;

        unset($function, $filter, $matches, $subMatches, $parameters, $text);
    }

}

# End of file