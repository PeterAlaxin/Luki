<?php
/**
 * Template Variable class
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

use Luki\Template;

class Variable
{
    protected $content             = '';
    protected $variable            = '';
    protected $functionName        = '';
    protected $function            = '';
    protected $transformedVariable = '';
    protected $code                = '';
    protected $filters             = array();
    protected $finalVariable       = '';

    public function __construct($content)
    {
        $this->content = $content;

        $this->prepareFilters();
        $this->transformVariable();
        $this->prepareVariable();
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getFunctionName()
    {
        return $this->functionName;
    }

    public function getFunction()
    {
        $this->prepareFunction();

        return $this->function;
    }

    public function getVariable()
    {
        return $this->finalVariable;
    }

    public function getVariableName()
    {
        return $this->transformedVariable;
    }

    public static function getVariableLenght($variable)
    {

        switch (gettype($variable)) {
            case 'array':
            case 'boolean':
            case 'null':
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
                if (is_a($variable, 'Luki\Data\MySQL\Result')) {
                    $lenght = $variable->getNumberOfRecords();
                }
                break;
            default :
                $lenght = null;
        }

        return $lenght;
    }

    private function prepareFilters()
    {
        if (strpos($this->content, '|')) {
            $this->filters      = explode('|', $this->content);
            $this->variable     = array_shift($this->filters);
            $this->functionName = 'fnc_'.sha1($this->content);
        } else {
            $this->variable = $this->content;
        }
    }

    private function transformVariable()
    {
        $matches = array();
        $hasKeys = false;
        preg_match('/^[\[{](.*)[\]}]$/', $this->variable, $matches);

        if (count($matches) > 0) {
            $newItems = array();
            $items    = explode(', ', $matches[1]);

            foreach ($items as $item) {
                if (strpos($item, ': ')) {
                    $hasKeys  = true;
                    $subItems = explode(': ', $item);

                    $newItems[] = $this->stringToVariable($subItems[0]).
                        ' => '.
                        $this->stringToVariable($subItems[1]);
                } else {
                    $newItems[] = $this->stringToVariable($item);
                }
            }

            $variable  = preg_replace('/[\[{]/', 'array(', $this->variable);
            $variables = preg_replace('/'.$matches[1].'/', implode(', ', $newItems), $variable);
            if ($hasKeys) {
                $this->transformedVariable = preg_replace('/[}]/', ')', $variables);
            } else {
                $this->transformedVariable = preg_replace('/[\]}]/', ')', $variables);
            }
        } else {
            $this->transformedVariable = $this->stringToVariable($this->variable);
        }
    }

    private function stringToVariable($string)
    {
        $types          = array('RangeOperator'        => '/^(.*)\.\.(.*)$/',
            'Range'                => '/^range\((.*)\)$/',
            'Random'               => '/^random\((.*)\)$/',
            'Constant'             => '/^constant\((.*)\)$/',
            'PathWithArguments'    => '/^path\((.*)(, )({.*})\)$/',
            'PathWithoutArguments' => '/^path\((.*)\)$/',
            'Date'                 => '/^date\((.*)(, )(.*)\)|date\((.*)\)$/',
            'Concat'               => '/^(.*) \~ (.*)$/',
            'SubArray'             => '/^(.*)\.(.*)$/'
        );
        $formatedString = '';

        if (!preg_match('/^[\'"]/', $string) and ! is_numeric($string)) {
            foreach ($types as $type => $regexp) {
                $matches = array();
                preg_match($regexp, $string, $matches);

                if (!empty($matches)) {
                    switch ($type) {
                        case 'Random':
                            $formatedString = '$this->aFunctions["random"]->Get('.$matches[1].')';
                            break;
                        case 'Range':
                            $formatedString = '$this->aFunctions["range"]->Get('.$matches[1].')';
                            break;
                        case 'Constant':
                            $formatedString = '$this->aFunctions["constant"]->Get('.$matches[1].')';
                            break;
                        case 'SubArray':
                            $items          = explode('.', $matches[0]);
                            $formatedString = '$this->aData["'.implode('"]["', $items).'"]';
                            break;
                        case 'RangeOperator':
                            $range          = explode('..', $string);
                            $newArray       = array();
                            if (is_numeric($range[0])) {
                                $min = min($range[0], $range[1]);
                                $max = max($range[0], $range[1]);
                                for ($i = $min; $i <= $max; $i++) {
                                    $newArray[] = $i;
                                }
                                $formatedString = 'array('.implode(',', $newArray).')';
                            } else {
                                $min = min(ord($range[0]), ord($range[1]));
                                $max = max(ord($range[0]), ord($range[1]));
                                for ($i = $min; $i <= $max; $i++) {
                                    $newArray[] = chr($i);
                                }
                                $formatedString = 'array("'.implode('","', $newArray).'")';
                            }
                            break;
                        case 'PathWithArguments':
                            $parameters     = preg_replace('/[\[{]/', 'array(', $matches[3]);
                            $parameters     = preg_replace('/: /', ' => ', $parameters);
                            $parameters     = preg_replace('/[\]}]/', ')', $parameters);
                            $parameters     = $this->fixDataInArray($parameters);
                            $formatedString = '$this->aFunctions["path"]->Get('.$this->stringToVariable($matches[1]).','.$parameters.')';
                            break;
                        case 'PathWithoutArguments':
                            $formatedString = '$this->aFunctions["path"]->Get('.$this->stringToVariable($matches[1]).')';
                            break;
                        case 'Concat':
                            $formatedString = '';
                            foreach (explode(' ~ ', $string) as $item) {
                                if (!empty($formatedString)) {
                                    $formatedString .= '.';
                                }
                                $formatedString .= $this->stringToVariable($item);
                            }
                            break;
                        case 'Date':
                            $date           = str_replace(array('"', '"'), array('', ''),
                                empty($matches[4]) ? $matches[1] : $matches[4]);
                            $zone           = str_replace(array('"', '"'), array('', ''),
                                empty($matches[3]) ? date_default_timezone_get() : $matches[3]);
                            $datetime       = new \DateTime($date, new \DateTimeZone($zone));
                            $formatedString = $datetime->format('U');
                            break;
                    }

                    break;
                }
            }

            if (empty($formatedString)) {
                $formatedString = '$this->aData["'.$string.'"]';
            }
        } else {
            $formatedString = $string;
        }

        return $formatedString;
    }

    private function fixDataInArray($arrayText)
    {
        $arrayText = substr($arrayText, 6, strlen($arrayText) - 7);
        $first     = explode(',', $arrayText);
        foreach ($first as $key => $item) {
            $second      = explode('=>', $item);
            $second[1]   = $this->stringToVariable(trim($second[1]));
            $first[$key] = implode('=> ', $second);
        }
        $fixedText = 'array('.implode(', ', $first).')';

        return $fixedText;
    }

    private function prepareVariable()
    {
        if (!empty($this->filters)) {
            $this->finalVariable = '$this->_'.$this->functionName.'('.$this->transformedVariable.')';
        } else {
            $this->finalVariable = $this->transformedVariable;
        }

        $this->code = '<?php echo '.$this->finalVariable.'; ?>';
    }

    private function prepareFunction()
    {
        $matches = array();

        $function = Template::phpRow('public function _'.$this->functionName.'($xValue)');
        $function .= Template::phpRow('{');
        foreach ($this->filters as $filter) {

            preg_match_all('|(.*)\((.*)\)|U', $filter, $matches, PREG_SET_ORDER);

            if (empty($matches)) {
                $function .= Template::phpRow('$xValue = $this->aFilters["'.$filter.'"]->Get($xValue);', 2);
            } else {
                if (empty($matches[0][2])) {
                    $text     = '$xValue = $this->aFilters["'.$matches[0][1].'"]->Get($xValue);';
                    $function .= Template::phpRow($text, 2);
                } else {
                    $parameters = $matches[0][2];
                    $subMatches = array();
                    preg_match('/^[\[{](.*)[\]}]$/', $parameters, $subMatches);

                    if (count($subMatches) > 0) {
                        $parameters = preg_replace('/[\[{]/', 'array(', $parameters);
                        $parameters = preg_replace('/: /', ' => ', $parameters);
                        $parameters = preg_replace('/[\]}]/', ')', $parameters);
                    }

                    preg_match_all("/\"[^\"]*\"|\'[^\']*\'|(array\()|\)|[a-z0-9\.]*|(=>)|(,)/", $parameters, $exploded);

                    $parameters = array();
                    foreach ($exploded[0] as $parameter) {

                        if ($parameter === '') {
                            continue;
                        }

                        if (is_numeric($parameter) or in_array($parameter, array('array(', '=>', ',', ')'))) {
                            $parameters[] = $parameter;
                        } elseif (strpos($parameter, 'app.') === 0) {
                            $items        = explode('.', $parameter);
                            $parameters[] = '$this->aData["'.implode('"]["', $items).'"]';
                        } elseif (strpos($parameter, '"') === 0 or strpos($parameter, "'") === 0) {
                            $parameters[] = $parameter;
                        } else {
                            $parameters[] = '$this->aData["'.$parameter.'"]';
                        }
                    }

                    $text     = '$xValue = $this->aFilters["'.$matches[0][1].'"]->Get($xValue, '.implode('', $parameters).');';
                    $function .= Template::phpRow($text, 2);
                }
            }
        }
        $function .= Template::phpRow('return $xValue;', 2);
        $function .= Template::phpRow('}', 1, 2);

        $this->function = $function;
    }
}