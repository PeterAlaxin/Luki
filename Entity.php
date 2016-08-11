<?php
/**
 * Entity class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Entity
 * @filesource
 */

namespace Luki;

use Luki\Data\BasicInterface;
use Luki\Template;

class Entity
{

    private $table;
    private $adapter;
    private $file;
    private $code;
    private $structure = array();

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function setData(BasicInterface $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    public function createEntity()
    {
        $this->readStructure();
        $this->startEntity();
        $this->addFunctions();
        $this->addSetters();
        $this->addGetters();
        $this->addFindBy();
        $this->addOneFindBy();
        $this->endEntity();
    }

    private function readStructure()
    {
        $structure = $this->adapter->getStructure($this->table);

        if (!empty($structure)) {
            foreach ($structure as $field) {
                $fieldName = camelCase($field['Field']);
                $this->structure[$fieldName] = $field;
            }
        }
    }

    private function startEntity()
    {
        $this->code = Template::phpRow('<?php ', 0);
        $this->code .= Template::phpRow('use Luki\Data\basicInterface;', 0);
        $this->code .= Template::phpRow('', 0);
        $this->code .= Template::phpRow('class ' . $this->table . 'Entity {', 0);
        $this->code .= Template::phpRow('', 0);

        $this->code .= Template::phpRow('private $data;');
        $this->code .= Template::phpRow('private $row = array();');
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctions()
    {
        $this->addFunctionConstruct();
        $this->addFunctionDefineRow();
        $this->addFunctionSetData();
        $this->addFunctionFind();
        $this->addFunctionFindBy();
        $this->addFunctionFindFulltext();
        $this->addFunctionFindOneBy();
        $this->addFunctionFindAll();
        $this->addFunctionGetRow();
        $this->addFunctionGetOriginalRow();
        $this->addFunctionFillRow();
        $this->addFunctionIsChanged();
        $this->addFunctionGetChanges();
        $this->addFunctionGetId();
        $this->addFunctionUpdate();
        $this->addFunctionCreate();
        $this->addFunctionInsert();
        $this->addFunctionDelete();
        $this->addFunctionCount();
        $this->addFunctionCountAll();
    }

    private function addFunctionConstruct()
    {
        $this->code .= Template::phpRow('public function __construct(basicInterface $dataAdapter) {');
        $this->code .= Template::phpRow('$this->setData($dataAdapter);', 2);
        $this->code .= Template::phpRow('$this->defineRow();', 2);
        $this->code .= Template::phpRow('$this->create();', 2);
        $this->code .= Template::phpRow('}');
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctionDefineRow()
    {
        $this->code .= Template::phpRow('private function defineRow() {');

        foreach ($this->structure as $field) {
            $this->code .= Template::phpRow('$this->row["' . $field['Field'] . '"]["Type"] = "' . $field['Type'] . '";', 2);
            $this->code .= Template::phpRow('$this->row["' . $field['Field'] . '"]["null"] = "' . $field['null'] . '";', 2);
            $this->code .= Template::phpRow('$this->row["' . $field['Field'] . '"]["Key"] = "' . $field['Key'] . '";', 2);
            $this->code .= Template::phpRow('$this->row["' . $field['Field'] . '"]["Default"] = "' . $field['Default'] . '";', 2);
            $this->code .= Template::phpRow('$this->row["' . $field['Field'] . '"]["Extra"] = "' . $field['Extra'] . '";', 2);
            $this->code .= Template::phpRow('$this->row["' . $field['Field'] . '"]["Changed"] = false;', 2);
            $this->code .= Template::phpRow('$this->row["' . $field['Field'] . '"]["Valid"] = false;', 2);
            $this->code .= Template::phpRow('$this->row["' . $field['Field'] . '"]["OriginalValue"] = null;', 2);
            $this->code .= Template::phpRow('$this->row["' . $field['Field'] . '"]["ActualValue"] = null;', 2);
        }

        $this->code .= Template::phpRow('}', 1);
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctionSetData()
    {
        $this->code .= Template::phpRow('private function setData($data) {');
        $this->code .= Template::phpRow('$this->data = $data;', 2);
        $this->code .= Template::phpRow('}');
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctionFind()
    {
        foreach ($this->structure as $field) {
            $this->code .= Template::phpRow('public function find($value) {');
            $this->code .= Template::phpRow('$select = $this->data->Select()->from("' . $this->table . '")->where("' . $field['Field'] . ' = ?", $value)->limit(1);', 2);
            $this->code .= Template::phpRow('$result = $this->data->Query($select);', 2);
            $this->code .= Template::phpRow('if(!$result) {', 2);
            $this->code .= Template::phpRow('$row = null;', 3);
            $this->code .= Template::phpRow('} else {', 2);
            $this->code .= Template::phpRow('$row = $result->getRow();', 3);
            $this->code .= Template::phpRow('$this->fillRow($row);', 3);
            $this->code .= Template::phpRow('}', 2);
            $this->code .= Template::phpRow('return $row;', 2);
            $this->code .= Template::phpRow('}', 1);
            $this->code .= Template::phpRow('', 0);

            break;
        }
    }

    private function addFunctionFindBy()
    {
        $this->code .= Template::phpRow('public function findBy($where, $order=null, $limit=0) {');
        $this->code .= Template::phpRow('$select = $this->data->Select()->from("' . $this->table . '");', 2);

        $this->code .= Template::phpRow('foreach($where as $key => $value) {', 2);
        $this->code .= Template::phpRow('$select->where("$key = \'?\'", $value);', 3);
        $this->code .= Template::phpRow('}', 2);

        $this->code .= Template::phpRow('if(!empty($order)) {', 2);
        $this->code .= Template::phpRow('foreach($order as $key => $value) {', 3);
        $this->code .= Template::phpRow('$select->order("$key $value");', 4);
        $this->code .= Template::phpRow('}', 3);
        $this->code .= Template::phpRow('}', 2);

        $this->code .= Template::phpRow('if(!empty($limit)) {', 2);
        $this->code .= Template::phpRow('$select->limit($limit);', 3);
        $this->code .= Template::phpRow('}', 2);

        $this->code .= Template::phpRow('$result = $this->data->Query($select);', 2);
        $this->code .= Template::phpRow('return $result;', 2);
        $this->code .= Template::phpRow('}', 1);
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctionFindFulltext()
    {
        $this->code .= Template::phpRow('public function findFulltext($fields, $value, $order=null) {');
        $this->code .= Template::phpRow('$select = $this->data->Select()->from("' . $this->table . '");', 2);

        $this->code .= Template::phpRow('if(is_array($fields)) {', 2);
        $this->code .= Template::phpRow('$fields = implode(",", $fields);', 3);
        $this->code .= Template::phpRow('}', 2);
        $this->code .= Template::phpRow('$value = "+" . implode("* +", explode(" ", $value)) . "*";', 2);
        $this->code .= Template::phpRow('$select->where("MATCH(" . $fields . ") AGAINST (\'?\' IN BOOLEAN MODE)", $value);', 2);

        $this->code .= Template::phpRow('if(!empty($order)) {', 2);
        $this->code .= Template::phpRow('foreach($order as $key => $value) {', 3);
        $this->code .= Template::phpRow('$select->order("$key $value");', 4);
        $this->code .= Template::phpRow('}', 3);
        $this->code .= Template::phpRow('}', 2);

        $this->code .= Template::phpRow('$result = $this->data->Query($select);', 2);
        $this->code .= Template::phpRow('return $result;', 2);
        $this->code .= Template::phpRow('}', 1);
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctionFindOneBy()
    {
        $this->code .= Template::phpRow('public function findOneBy($where, $order=null) {');
        $this->code .= Template::phpRow('$select = $this->data->Select()->from("' . $this->table . '")->limit(1);', 2);
        $this->code .= Template::phpRow('foreach($where as $key => $value) {', 2);
        $this->code .= Template::phpRow('$select->where("$key = \'?\'", $value);', 3);
        $this->code .= Template::phpRow('}', 2);

        $this->code .= Template::phpRow('if(!empty($order)) {', 2);
        $this->code .= Template::phpRow('foreach($order as $key => $value) {', 3);
        $this->code .= Template::phpRow('$select->order("$key $value");', 4);
        $this->code .= Template::phpRow('}', 3);
        $this->code .= Template::phpRow('}', 2);

        $this->code .= Template::phpRow('$result = $this->data->Query($select);', 2);
        $this->code .= Template::phpRow('if(!$result) {', 2);
        $this->code .= Template::phpRow('$row = null;', 3);
        $this->code .= Template::phpRow('} else {', 2);
        $this->code .= Template::phpRow('$row = $result->getRow();', 3);
        $this->code .= Template::phpRow('$this->fillRow($row);', 3);
        $this->code .= Template::phpRow('}', 2);

        $this->code .= Template::phpRow('return $row;', 2);
        $this->code .= Template::phpRow('}', 1);
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctionFindAll()
    {
        $this->code .= Template::phpRow('public function findAll($order=null) {');
        $this->code .= Template::phpRow('$select = $this->data->Select()->from("' . $this->table . '");', 2);

        $this->code .= Template::phpRow('if(!empty($order)) {', 2);
        $this->code .= Template::phpRow('foreach($order as $key => $value) {', 3);
        $this->code .= Template::phpRow('$select->order("$key $value");', 4);
        $this->code .= Template::phpRow('}', 3);
        $this->code .= Template::phpRow('}', 2);

        $this->code .= Template::phpRow('$result = $this->data->Query($select);', 2);
        $this->code .= Template::phpRow('return $result;', 2);
        $this->code .= Template::phpRow('}', 1);
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctionGetRow()
    {
        $this->code .= Template::phpRow('public function getRow() {');
        $this->code .= Template::phpRow('$row = array();', 2);
        $this->code .= Template::phpRow('foreach($this->row as $key => $column) {', 2);
        $this->code .= Template::phpRow('$row[$key] = $column["ActualValue"];', 3);
        $this->code .= Template::phpRow('};', 2);
        $this->code .= Template::phpRow('return $row;', 2);
        $this->code .= Template::phpRow('}', 1);
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctionGetOriginalRow()
    {
        $this->code .= Template::phpRow('public function getOriginalRow() {');
        $this->code .= Template::phpRow('$row = array();', 2);
        $this->code .= Template::phpRow('foreach($this->row as $key => $column) {', 2);
        $this->code .= Template::phpRow('$row[$key] = $column["OriginalValue"];', 3);
        $this->code .= Template::phpRow('};', 2);
        $this->code .= Template::phpRow('return $row;', 2);
        $this->code .= Template::phpRow('}', 1);
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctionGetChanges()
    {
        $this->code .= Template::phpRow('public function getChanges() {');
        $this->code .= Template::phpRow('$row = array();', 2);
        $this->code .= Template::phpRow('foreach($this->row as $key => $column) {', 2);
        $this->code .= Template::phpRow('if($column["Changed"]) {', 3);
        $this->code .= Template::phpRow('$row[$key]["from"] = $column["OriginalValue"];', 4);
        $this->code .= Template::phpRow('$row[$key]["to"] = $column["ActualValue"];', 4);
        $this->code .= Template::phpRow('};', 3);
        $this->code .= Template::phpRow('};', 2);
        $this->code .= Template::phpRow('return $row;', 2);
        $this->code .= Template::phpRow('}', 1);
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctionFillRow()
    {
        $this->code .= Template::phpRow('public function fillRow($row) {');
        $this->code .= Template::phpRow('if(is_array($row)) {', 2);
        $this->code .= Template::phpRow('foreach($row as $key => $value) {', 3);
        $this->code .= Template::phpRow('$this->row[$key]["Changed"] = false;', 4);
        $this->code .= Template::phpRow('$this->row[$key]["Valid"] = false;', 4);
        $this->code .= Template::phpRow('$this->row[$key]["OriginalValue"] = $value;', 4);
        $this->code .= Template::phpRow('$this->row[$key]["ActualValue"] = $value;', 4);
        $this->code .= Template::phpRow('}', 3);
        $this->code .= Template::phpRow('}', 2);
        $this->code .= Template::phpRow('}', 1);
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctionIsChanged()
    {
        $this->code .= Template::phpRow('public function isChanged() {');
        $this->code .= Template::phpRow('$changed = false;', 2);
        $this->code .= Template::phpRow('foreach($this->row as $key => $value) {', 2);
        $this->code .= Template::phpRow('if($this->row[$key]["Changed"] === true) {', 3);
        $this->code .= Template::phpRow('$changed = true;', 4);
        $this->code .= Template::phpRow('break;', 4);
        $this->code .= Template::phpRow('}', 3);
        $this->code .= Template::phpRow('}', 2);
        $this->code .= Template::phpRow('return $changed;', 2);
        $this->code .= Template::phpRow('}', 1);
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctionGetId()
    {
        foreach ($this->structure as $field) {
            $this->code .= Template::phpRow('public function getId() {');
            $this->code .= Template::phpRow('return $this->row["' . $field['Field'] . '"]["ActualValue"];', 2);
            $this->code .= Template::phpRow('}', 1);
            $this->code .= Template::phpRow('', 0);
            break;
        }
    }

    private function addFunctionUpdate()
    {
        $this->code .= Template::phpRow('public function update() {');
        $this->code .= Template::phpRow('$changes = $this->getChanges();', 2);
        $this->code .= Template::phpRow('$data = array();', 2);
        $this->code .= Template::phpRow('foreach($changes as $column => $values) {', 2);
        $this->code .= Template::phpRow('$data["$column"] = $values["to"];', 3);
        $this->code .= Template::phpRow('}', 2);

        foreach ($this->structure as $field) {
            $this->code .= Template::phpRow('$where = array("' . $field['Field'] . '" => $this->getId());', 2);
            break;
        }

        $this->code .= Template::phpRow('$this->data->Update("' . $this->table . '", $data, $where);', 2);
        $this->code .= Template::phpRow('return $this;', 2);
        $this->code .= Template::phpRow('}', 1);
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctionCreate()
    {
        $this->code .= Template::phpRow('public function create() {');
        $this->code .= Template::phpRow('foreach($this->row as $key => $column) {', 2);
        $this->code .= Template::phpRow('$this->row[$key]["Changed"] = false;', 3);
        $this->code .= Template::phpRow('$this->row[$key]["Valid"] = false;', 3);
        $this->code .= Template::phpRow('$this->row[$key]["OriginalValue"] = $column["Default"];', 3);
        $this->code .= Template::phpRow('$this->row[$key]["ActualValue"] = $column["Default"];', 3);
        $this->code .= Template::phpRow('};', 2);
        $this->code .= Template::phpRow('return $this;', 2);
        $this->code .= Template::phpRow('}', 1);
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctionInsert()
    {
        $this->code .= Template::phpRow('public function insert() {');
        $this->code .= Template::phpRow('$data = array();', 2);
        $this->code .= Template::phpRow('foreach($this->row as $column => $values) {', 2);
        $this->code .= Template::phpRow('$data["$column"] = $values["ActualValue"];', 3);
        $this->code .= Template::phpRow('}', 2);
        $this->code .= Template::phpRow('$this->data->Insert("' . $this->table . '", $data);', 2);
        $this->code .= Template::phpRow('$id = $this->data->getLastID("' . $this->table . '");', 2);
        $this->code .= Template::phpRow('$this->find($id);', 2);
        $this->code .= Template::phpRow('return $this;', 2);
        $this->code .= Template::phpRow('}', 1);
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctionDelete()
    {
        $this->code .= Template::phpRow('public function delete() {');

        foreach ($this->structure as $field) {
            $this->code .= Template::phpRow('$this->data->Delete("' . $this->table . '", array("' . $field['Field'] . '" => $this->getId()));', 2);
            break;
        }

        $this->code .= Template::phpRow('$this->create();', 2);
        $this->code .= Template::phpRow('return $this;', 2);
        $this->code .= Template::phpRow('}', 1);
        $this->code .= Template::phpRow('', 0);
    }

    private function addSetters()
    {
        $isFirst = true;
        foreach ($this->structure as $fielName => $field) {
            if ($isFirst) {
                $isFirst = false;
            }
            $this->code .= Template::phpRow('public function set' . $fielName . '($value) {');
            $this->code .= Template::phpRow('$this->row["' . $field['Field'] . '"]["ActualValue"] = $value;', 2);
            $this->code .= Template::phpRow('$this->row["' . $field['Field'] . '"]["Changed"] = true;', 2);
            $this->code .= Template::phpRow('return $this;', 2);
            $this->code .= Template::phpRow('}', 1);
            $this->code .= Template::phpRow('', 0);
        }
    }

    private function addGetters()
    {
        foreach ($this->structure as $fielName => $field) {
            $this->code .= Template::phpRow('public function get' . $fielName . '() {');
            $this->code .= Template::phpRow('return $this->row["' . $field['Field'] . '"]["ActualValue"];', 2);
            $this->code .= Template::phpRow('}', 1);
            $this->code .= Template::phpRow('', 0);
        }
    }

    private function addFindBy()
    {
        foreach ($this->structure as $fielName => $field) {
            $this->code .= Template::phpRow('public function findBy' . $fielName . '($value, $order=null, $limit=0) {');
            $this->code .= Template::phpRow('$select = $this->data->Select()->from("' . $this->table . '")->where("' . $field['Field'] . ' = \'?\'", $value);', 2);

            $this->code .= Template::phpRow('if(!empty($order)) {', 2);
            $this->code .= Template::phpRow('foreach($order as $key => $value) {', 3);
            $this->code .= Template::phpRow('$select->order("$key $value");', 4);
            $this->code .= Template::phpRow('}', 3);
            $this->code .= Template::phpRow('}', 2);

            $this->code .= Template::phpRow('if(!empty($limit)) {', 2);
            $this->code .= Template::phpRow('$select->limit($limit);', 3);
            $this->code .= Template::phpRow('}', 2);

            $this->code .= Template::phpRow('$result = $this->data->Query($select);', 2);
            $this->code .= Template::phpRow('return $result;', 2);
            $this->code .= Template::phpRow('}', 1);
            $this->code .= Template::phpRow('', 0);
        }
    }

    private function addOneFindBy()
    {
        foreach ($this->structure as $fielName => $field) {
            $this->code .= Template::phpRow('public function findOneBy' . $fielName . '($value, $order=null) {');
            $this->code .= Template::phpRow('$select = $this->data->Select()->from("' . $this->table . '")->where("' . $field['Field'] . ' = \'?\'", $value)->limit(1);', 2);

            $this->code .= Template::phpRow('if(!empty($order)) {', 2);
            $this->code .= Template::phpRow('foreach($order as $key => $value) {', 3);
            $this->code .= Template::phpRow('$select->order("$key $value");', 4);
            $this->code .= Template::phpRow('}', 3);
            $this->code .= Template::phpRow('}', 2);

            $this->code .= Template::phpRow('$result = $this->data->Query($select);', 2);
            $this->code .= Template::phpRow('if(!$result) {', 2);
            $this->code .= Template::phpRow('$row = null;', 3);
            $this->code .= Template::phpRow('} else {', 2);
            $this->code .= Template::phpRow('$row = $result->getRow();', 3);
            $this->code .= Template::phpRow('$this->fillRow($row);', 3);
            $this->code .= Template::phpRow('}', 2);

            $this->code .= Template::phpRow('return $row;', 2);
            $this->code .= Template::phpRow('}', 1);
            $this->code .= Template::phpRow('', 0);
        }
    }

    private function endEntity()
    {
        $this->code .= Template::phpRow('public function __destruct()');
        $this->code .= Template::phpRow('{');
        $this->code .= Template::phpRow('foreach ( $this as &$value ) {', 2);
        $this->code .= Template::phpRow('$value = null;', 3);
        $this->code .= Template::phpRow('}', 2);
        $this->code .= Template::phpRow('}');

        $this->code .= Template::phpRow('', 0);
        $this->code .= Template::phpRow('}', 0);

        file_put_contents($this->file, $this->code);
    }

    private function addFunctionCount()
    {
        $this->code .= Template::phpRow('public function count($where) {');
        $this->code .= Template::phpRow('$select = $this->data->Select()->from("' . $this->table . '", array("SUM(1) AS counter"));', 2);

        $this->code .= Template::phpRow('foreach($where as $key => $value) {', 2);
        $this->code .= Template::phpRow('$select->where("$key = \'?\'", $value);', 3);
        $this->code .= Template::phpRow('}', 2);

        $this->code .= Template::phpRow('$counter = $this->data->Query($select)->Get("counter");', 2);
        $this->code .= Template::phpRow('return $counter;', 2);
        $this->code .= Template::phpRow('}', 1);
        $this->code .= Template::phpRow('', 0);
    }

    private function addFunctionCountAll()
    {
        $this->code .= Template::phpRow('public function countAll() {');
        $this->code .= Template::phpRow('$select = $this->data->Select()->from("' . $this->table . '", array("SUM(1) AS counter"));', 2);
        $this->code .= Template::phpRow('$counter = $this->data->Query($select)->Get("counter");', 2);
        $this->code .= Template::phpRow('return $counter;', 2);
        $this->code .= Template::phpRow('}', 1);
        $this->code .= Template::phpRow('', 0);
    }
}
