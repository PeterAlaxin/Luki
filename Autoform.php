<?php
/**
 * Autoform class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Autoform
 * @filesource
 */

namespace Luki;

use Luki\Data\BasicInterface;
use Luki\Formular\Checkbox as checkboxInput;
use Luki\Formular\Date as dateInput;
use Luki\Formular\DateTime as datetimeInput;
use Luki\Formular\Number as numberInput;
use Luki\Formular\Text as textInput;
use Luki\Formular\Textarea as textareaInput;
use Luki\Formular\Time as timeInput;
use Luki\Formular\Select as selectInput;
use Luki\Storage;

class Autoform
{
    private $adapter;
    private $class;
    private $default;
    private $field;
    private $inputs = [];
    private $limit;
    private $max;
    private $min;
    private $null;
    private $parenthesis;
    private $pattern;
    private $size;
    private $step;
    private $structure;
    private $table;
    private $type;
    private $unsigned;

    public function __construct($table, BasicInterface $adapter)
    {
        $this->table   = $table;
        $this->adapter = $adapter;

        if (!empty($table)) {
            if (!$this->isCached()) {
                $this->readStructure();
                $this->setInputs();
                $this->setToCache();
            }
        }
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    public function setValues($values)
    {
        foreach ($values as $name => $value) {
            if (array_key_exists($name, $this->inputs)) {
                $this->inputs[$name]['value'] = $value;
            }
        }

        return $this;
    }

    public function getInput($field)
    {
        $input = null;

        if (array_key_exists($field, $this->inputs)) {
            $input = $this->createInputObject($field);
        }

        return $input;
    }

    public function getAllInputs()
    {
        $inputs = null;

        foreach (array_keys($this->inputs) as $field) {
            $inputs[$field] = $this->createInputObject($field);
        }

        return $inputs;
    }

    private function isCached()
    {
        $cached = false;

        if (Storage::isCache() and Storage::Cache()->isUsedCache()) {
            $inputs = Storage::Cache()->Get('Autoform_'.$this->table);
            if (!empty($inputs)) {
                $this->inputs = $inputs;
                $cached       = true;
            }
        }

        return $cached;
    }

    private function setToCache()
    {
        if (Storage::isCache()) {
            Storage::Cache()->Set('Autoform_'.$this->table, $this->inputs, 3600);
        }
    }

    private function readStructure()
    {
        $structure = $this->adapter->getStructure($this->table);

        if (!empty($structure)) {
            foreach ($structure as $field) {
                if ('id' == $field['Field']) {
                    continue;
                }
                $this->structure[camelCase($field['Field'])] = $field;
            }
        }
    }

    private function setInputs()
    {
        foreach ($this->structure as $name => $input) {
            $this->prepareInput($input);
            $this->fillInput();
        }
    }

    private function prepareInput($input)
    {
        $this->clear();

        $this->field = $input['Field'];
        $this->setType($input['Type']);
        $this->setUnsigned($input['Type']);
        $this->setSize($input['Type']);
        $this->setMinMax();
        $this->setNull($input['Null']);
        $this->setDefault($input['Default']);
        $this->setComment($input['Comment']);
    }

    private function clear()
    {
        $this->default     = null;
        $this->field       = null;
        $this->limit       = null;
        $this->max         = null;
        $this->min         = null;
        $this->null        = null;
        $this->parenthesis = null;
        $this->pattern     = null;
        $this->size        = null;
        $this->step        = null;
        $this->type        = null;
        $this->unsigned    = null;
    }

    private function setType($text)
    {
        $this->parenthesis = strpos($text, '(');

        if ($this->parenthesis === false) {
            $this->type = $text;
        } else {
            $this->type = substr($text, 0, $this->parenthesis);
        }
    }

    private function setUnsigned($text)
    {
        $this->unsigned = (bool) strpos($text, 'unsigned');
    }

    private function setSize($text)
    {
        if ($this->parenthesis !== false) {
            preg_match('/\d+,\d+|\d+/', $text, $output);
            $this->size = $output[0];
        } else {
            if ('tinytext' == $this->type or 'char' == $this->type or 'varchar' == $this->type) {
                $this->size = 255;
            } else if ('text' == $this->type or 'blob' == $this->type) {
                $this->size = 65535;
            } else if ('mediumtext' == $this->type or 'mediumblob' == $this->type) {
                $this->size = 16777215;
            } else if ('longtext' == $this->type or 'longblob' == $this->type) {
                $this->size = 4294967295;
            } else {
                $this->size = 0;
            }
        }
    }

    private function setMinMax()
    {
        if (in_array($this->type, ['tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'decimal', 'boolean'])) {
            $this->setLimit();
            if ($this->unsigned) {
                $this->setMinMaxUnsigned();
            } else {
                $this->setMinMaxSigned();
            }
        } else {
            $this->min = null;
            $this->max = null;
        }
    }

    private function setLimit()
    {
        if (strpos($this->size, ',') === false) {
            $this->limit = bcsub('1'.str_repeat('0', $this->size), 1);
        } else {
            $this->limit = bcsub('1'.str_repeat('0', (int) $this->size - 2), '0.01', 2);
        }
    }

    private function setMinMaxSigned()
    {
        switch ($this->type) {
            case 'boolean';
            case 'tinyint';
                $min = -128;
                $max = 127;
                break;
            case 'smallint';
                $min = -32768;
                $max = 32767;
                break;
            case 'mediumint';
                $min = -8388608;
                $max = 8388607;
                break;
            case 'int';
                $min = -2147483648;
                $max = 2147483647;
                break;
            case 'bigint';
                $min = pow(2, 63);
                $max = pow(2, 63) - 1;
                break;
            case 'decimal';
                $min = bcmul($this->limit, -1, 2);
                $max = $this->limit;
                break;
        }

        $this->min = max($min, -$this->limit);
        $this->max = min($max, $this->limit);
    }

    private function setMinMaxUnsigned()
    {
        switch ($this->type) {
            case 'boolean';
            case 'tinyint';
                $max = 255;
                break;
            case 'smallint';
                $max = 65535;
                break;
            case 'mediumint';
                $max = 16777215;
                break;
            case 'int';
                $max = 4294967295;
                break;
            case 'bigint';
                $max = $this->limit;
                break;
        }

        $this->min = 0;
        $this->max = min($max, $this->limit);
    }

    private function setNull($text)
    {
        $this->null = ('YES' == $text);
    }

    private function setDefault($text)
    {
        $this->default = $text;
    }

    private function setComment($text)
    {
        $comment = [];
        if (!empty($text)) {
            foreach (explode("\n", $text) as $item) {
                list($key, $value) = explode(":", $item);

                switch ($key) {
                    case 'pattern':
                        $this->pattern = $value;
                        break;
                    case 'min':
                        $this->min     = max($min, $value);
                        break;
                    case 'max':
                        $this->max     = min($max, $value);
                        break;
                }
            }
        }
    }

    private function fillInput()
    {
        $this->inputs[$this->field] = [
            'default'  => $this->default,
            'max'      => $this->max,
            'min'      => $this->min,
            'null'     => $this->null,
            'pattern'  => $this->pattern,
            'size'     => $this->size,
            'step'     => $this->step,
            'type'     => $this->type,
            'unsigned' => $this->unsigned,
            'value'    => $this->default,
        ];
    }

    private function createInputObject($field)
    {
        $source = $this->inputs[$field];

        switch ($source['type']) {
            case 'date':
                $input = $this->createInputDate($field);
                break;
            case 'datetime':
            case 'timestamp':
                $input = $this->createInputDatetime($field);
                break;
            case 'bigint':
            case 'boolean':
            case 'int':
            case 'smallint':
            case 'tinyint':
            case 'mediumint':
                if ('_id' == substr($field, -3)) {
                    $input = $this->createInputSelect($field);
                } else if (1 == $source['size']) {
                    $input = $this->createInputCheckbox($field);
                } else {
                    $input = $this->createInputNumber($field, $source);
                }
                break;
            case 'decimal':
            case 'double':
            case 'float':
                $input = $this->createInputNumber($field, $source);
                break;
            case 'blob':
            case 'longblob':
            case 'longtext':
            case 'mediumblob':
            case 'mediumtext':
            case 'text':
                $input = $this->createInputTextarea($field, $source);
                break;
            case 'char':
            case 'tinytext':
            case 'varchar':
                $input = $this->createInputText($field, $source);
                break;
            case 'enum':
            case 'set':
                $input = $this->createInputSelect($field);
                break;
            case 'time':
                $input = $this->createInputTime($field);
                break;
            default:
                exit;
        }

        $input->setValue($source['value']);

        if (!empty($placeholder = _ta($field.'_placeholder', $this->table))) {
            $input->setAttribute('placeholder', $placeholder);
        }

        if (!empty($error = _ta($field.'_error', $this->table))) {
            $input->setAttribute('oninvalid', "this.setCustomValidity('$error')");
            $input->setAttribute('oninput', "this.setCustomValidity('')");
        }

        if (!empty($hint = _ta($field.'_hint', $this->table))) {
            $input->setHint($hint);
        }

        if (!empty($this->class)) {
            $input->setAttribute('class', $this->class);
        }

        return $input;
    }

    private function createInputSelect($field)
    {
        $input = new selectInput($field, _ta($field, $this->table));

        if (false === $this->null) {
            $input->setAttribute('required', 'required');
        }

        return $input;
    }

    private function createInputNumber($field, $source)
    {
        $input = new numberInput($field, _ta($field, $this->table));

        if (!empty($source['min'])) {
            $input->setAttribute('min', $source['min']);
        }

        if (!empty($source['max'])) {
            $input->setAttribute('max', $source['max']);
        }

        if (false === $source['null']) {
            $input->setAttribute('required', 'required');
        }

        if (!empty($source['pattern'])) {
            $input->setAttribute('pattern', $source['pattern']);
        }

        if (!empty($source['size'])) {
            $input->setAttribute('size', $source['size']);
        }

        if (!empty($source['step'])) {
            $input->setAttribute('step', $source['step']);
        }

        return $input;
    }

    private function createInputText($field, $source)
    {
        $input = new textInput($field, _ta($field, $this->table));

        if (false === $source['null']) {
            $input->setAttribute('required', 'required');
        }

        if (!empty($source['pattern'])) {
            $input->setAttribute('pattern', $source['pattern']);
        }

        if (!empty($source['size'])) {
            $input->setAttribute('maxlength', $source['size']);
        }

        return $input;
    }

    private function createInputTextarea($field, $source)
    {
        $input = new textareaInput($field, _ta($field, $this->table));

        if (false === $source['null']) {
            $input->setAttribute('required', 'required');
        }

        return $input;
    }

    private function createInputCheckbox($field)
    {
        $input = new checkboxInput($field, _ta($field, $this->table));

        return $input;
    }

    private function createInputDate($field)
    {
        $input = new dateInput($field, _ta($field, $this->table));

        return $input;
    }

    private function createInputDatetime($field)
    {
        $input = new datetimeInput($field, _ta($field, $this->table));

        return $input;
    }

    private function createInputTime($field)
    {
        $input = new timeInput($field, _ta($field, $this->table));

        return $input;
    }
}